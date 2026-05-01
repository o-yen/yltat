<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Talent;
use App\Models\InternshipFeedback;
use App\Models\TrainingParticipant;
use App\Models\LogbookUpload;
use App\Models\KehadiranPrestasi;
use App\Models\KewanganElaun;
use App\Models\StatusSurat;
use App\Models\BudgetTransaction;
use App\Models\SyarikatPelaksana;
use App\Models\SyarikatPenempatan;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ManagePlacementController extends Controller
{
    public function index(Request $request)
    {
        $query = Talent::with(['syarikatPelaksana', 'syarikatPenempatan'])
            ->whereNotNull('id_graduan');

        // Scope by company role
        $user = auth()->user();
        $role = $user->role?->role_name;
        if ($role === 'rakan_kolaborasi' && $user->id_syarikat_penempatan) {
            $query->where('id_syarikat_penempatan', $user->id_syarikat_penempatan);
        } elseif ($role === 'syarikat_pelaksana' && $user->id_pelaksana) {
            $query->where('id_pelaksana', $user->id_pelaksana);
        }

        // Filter: assigned vs not assigned
        if ($request->filled('assignment')) {
            if ($request->assignment === 'assigned') {
                $query->whereNotNull('id_syarikat_penempatan')->where('id_syarikat_penempatan', '!=', '');
            } elseif ($request->assignment === 'not_assigned') {
                $query->where(function ($q) {
                    $q->whereNull('id_syarikat_penempatan')->orWhere('id_syarikat_penempatan', '');
                });
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('id_graduan', 'like', "%{$search}%")
                  ->orWhere('talent_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }

        $talents = $query->orderBy('full_name')->paginate(20)->withQueryString();
        $isCompanyRole = in_array($role, ['rakan_kolaborasi', 'syarikat_pelaksana']);

        return view('admin.manage-placement.index', compact('talents', 'isCompanyRole'));
    }

    public function show(Talent $talent)
    {
        $this->authorizeAccess($talent);

        $talent->load(['syarikatPelaksana', 'syarikatPenempatan', 'documents']);
        try { $talent->load('certifications'); } catch (\Throwable $e) { /* table may not exist */ }

        // Logbook
        $logbooks = LogbookUpload::where('id_graduan', $talent->id_graduan)
            ->orderByDesc('tahun')->orderByDesc('id')->get();

        // Daily logs
        $dailyLogs = $talent->dailyLogs()->orderByDesc('log_date')->limit(20)->get();

        // Transactions (kewangan elaun)
        $transactions = KewanganElaun::where('id_graduan', $talent->id_graduan)
            ->orderByDesc('tahun')->orderByDesc('id')->get();

        // Training
        $trainings = TrainingParticipant::with('trainingRecord')
            ->where('id_graduan', $talent->id_graduan)->get();

        // Status Surat (Surat Kuning & Biru)
        $suratRecords = StatusSurat::where('id_graduan', $talent->id_graduan)
            ->orderByDesc('tarikh_mula_proses')->get();

        // Feedback
        $placements = $talent->placements()->with('feedback')->get();
        $feedbacks = $placements->flatMap(fn($p) => $p->feedback);

        // Budget transactions
        $budgetTransactions = $talent->budgetTransactions()->orderByDesc('transaction_date')->get();

        $role = auth()->user()->role?->role_name;
        $canWrite = in_array(
            \App\Http\Middleware\ModuleAccess::levelFor(auth()->user()->role?->role_name, 'placements'),
            ['full', 'edit', 'own', 'create']
        );
        $canAssign = in_array($role, ['super_admin', 'pmo_admin', 'syarikat_pelaksana'], true);
        $canOverridePelaksana = in_array($role, ['super_admin', 'pmo_admin'], true);

        $isAssigned = !empty($talent->id_syarikat_penempatan);
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $penempatan = SyarikatPenempatan::orderBy('nama_syarikat')->get();

        return view('admin.manage-placement.show', compact(
            'talent', 'logbooks', 'dailyLogs', 'transactions', 'trainings',
            'suratRecords', 'feedbacks', 'budgetTransactions', 'canWrite',
            'canAssign', 'canOverridePelaksana', 'isAssigned', 'pelaksana', 'penempatan'
        ));
    }

    public function storeFeedback(Request $request, Talent $talent)
    {
        $this->authorizeAccess($talent);

        $request->validate([
            'placement_id' => 'required|exists:placements,id',
            'feedback_from' => 'required|in:company,yltat',
            'score_technical' => 'required|integer|min:1|max:5',
            'score_communication' => 'required|integer|min:1|max:5',
            'score_discipline' => 'required|integer|min:1|max:5',
            'score_problem_solving' => 'required|integer|min:1|max:5',
            'score_professionalism' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
        ]);

        InternshipFeedback::create([
            'placement_id' => $request->placement_id,
            'feedback_from' => $request->feedback_from,
            'score_technical' => $request->score_technical,
            'score_communication' => $request->score_communication,
            'score_discipline' => $request->score_discipline,
            'score_problem_solving' => $request->score_problem_solving,
            'score_professionalism' => $request->score_professionalism,
            'comments' => $request->comments,
            'submitted_at' => now(),
        ]);

        return back()->with('success', __('messages.feedback_submitted'));
    }

    public function assignPlacement(Request $request, Talent $talent)
    {
        $this->authorizeAccess($talent);

        $user = auth()->user();
        $role = $user->role?->role_name;

        if (!in_array($role, ['super_admin', 'pmo_admin', 'syarikat_pelaksana'], true)) {
            abort(403, 'Only Admin / PMO or the implementing company can assign approved talents.');
        }

        if ($role === 'syarikat_pelaksana') {
            if (!$user->id_pelaksana || $talent->id_pelaksana !== $user->id_pelaksana) {
                abort(403, 'You can only assign talents approved for your implementation company.');
            }
        }

        $request->validate([
            'id_pelaksana' => 'nullable|string',
            'id_syarikat_penempatan' => 'required|string|exists:syarikat_penempatan,id_syarikat',
            'jawatan' => 'nullable|string|max:200',
            'tarikh_mula' => 'nullable|date',
            'tarikh_tamat' => 'nullable|date|after_or_equal:tarikh_mula',
            'department' => 'nullable|string|max:200',
            'supervisor_name' => 'nullable|string|max:200',
            'supervisor_email' => 'nullable|email|max:200',
            'monthly_stipend' => 'nullable|numeric|min:0',
            'status_penyerapan_6bulan' => 'nullable|in:Diserap,Tidak Diserap,Dalam Proses,Belum Layak',
        ]);

        $old = $talent->toArray();
        $pelaksanaId = in_array($role, ['super_admin', 'pmo_admin'], true)
            ? ($request->id_pelaksana ?: $talent->id_pelaksana)
            : $talent->id_pelaksana;

        $updateData = [
            'id_pelaksana' => $pelaksanaId,
            'id_syarikat_penempatan' => $request->id_syarikat_penempatan,
            'jawatan' => $request->jawatan,
            'tarikh_mula' => $request->tarikh_mula,
            'tarikh_tamat' => $request->tarikh_tamat,
            'department' => $request->department,
            'supervisor_name' => $request->supervisor_name,
            'supervisor_email' => $request->supervisor_email,
            'monthly_stipend' => $request->monthly_stipend,
            'status_aktif' => $talent->status_aktif ?: 'Aktif',
        ];

        if ($request->filled('status_penyerapan_6bulan')) {
            $updateData['status_penyerapan_6bulan'] = $request->status_penyerapan_6bulan;
        }

        $talent->update($updateData);

        AuditLog::log('talents', 'assign_placement', $talent->id, $old, $talent->fresh()->toArray());

        return back()->with('success', __('messages.placement_assigned'));
    }

    public function completePlacement(Request $request, Talent $talent)
    {
        $this->authorizeAccess($talent);

        $request->validate([
            'completion_date' => 'required|date',
            'status_penyerapan_6bulan' => 'required|in:Diserap,Tidak Diserap,Dalam Proses,Belum Layak',
            'completion_remarks' => 'nullable|string|max:1000',
        ]);

        $old = $talent->toArray();

        $talent->update([
            'status_aktif' => 'Tamat',
            'tarikh_tamat' => $request->completion_date,
            'status_penyerapan_6bulan' => $request->status_penyerapan_6bulan,
        ]);

        // Also update placement record if exists
        $talent->placements()->whereIn('placement_status', ['active', 'confirmed'])->update([
            'placement_status' => 'completed',
            'end_date' => $request->completion_date,
            'remarks' => $request->completion_remarks,
        ]);

        AuditLog::log('talents', 'complete_placement', $talent->id, $old, $talent->fresh()->toArray());

        return back()->with('success', __('messages.placement_completed'));
    }

    public function earlyTermination(Request $request, Talent $talent)
    {
        $this->authorizeAccess($talent);

        $request->validate([
            'termination_date' => 'required|date',
            'termination_reason' => 'required|string|max:500',
        ]);

        $old = $talent->toArray();

        $talent->update([
            'status_aktif' => 'Berhenti Awal',
            'tarikh_tamat' => $request->termination_date,
        ]);

        $talent->placements()->whereIn('placement_status', ['active', 'confirmed'])->update([
            'placement_status' => 'terminated',
            'end_date' => $request->termination_date,
            'remarks' => $request->termination_reason,
        ]);

        AuditLog::log('talents', 'early_termination', $talent->id, $old, $talent->fresh()->toArray());

        return back()->with('success', __('messages.placement_terminated'));
    }

    private function authorizeAccess(Talent $talent): void
    {
        $user = auth()->user();
        $role = $user->role?->role_name;

        if ($role === 'rakan_kolaborasi' && $user->id_syarikat_penempatan) {
            if ($talent->id_syarikat_penempatan !== $user->id_syarikat_penempatan) abort(403);
        }
        if ($role === 'syarikat_pelaksana' && $user->id_pelaksana) {
            if ($talent->id_pelaksana !== $user->id_pelaksana) abort(403);
        }
    }
}
