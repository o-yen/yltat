<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Talent;
use App\Models\TrainingRecord;
use App\Models\TrainingParticipant;
use App\Models\SyarikatPenempatan;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class TrainingRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainingRecord::with('syarikatPenempatan')->withCount('participants');

        // Scope by company role
        $user = auth()->user();
        $role = $user->role?->role_name;
        if ($role === 'rakan_kolaborasi') {
            $idSyarikat = $user->id_syarikat_penempatan;
            $query->where('id_syarikat', $idSyarikat ?: '__none__');
        } elseif ($role === 'syarikat_pelaksana') {
            $companyIds = Talent::where('id_pelaksana', $user->id_pelaksana)->pluck('id_syarikat_penempatan')->filter()->unique()->toArray();
            $query->whereIn('id_syarikat', $companyIds ?: ['__none__']);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_training', 'like', "%{$search}%")
                  ->orWhere('tajuk_training', 'like', "%{$search}%")
                  ->orWhere('nama_syarikat', 'like', "%{$search}%");
            });
        }
        if ($request->filled('id_syarikat')) {
            $query->where('id_syarikat', $request->id_syarikat);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('sesi')) {
            $query->where('sesi', $request->sesi);
        }

        $records = $query->orderByDesc('tarikh_training')->paginate(20)->withQueryString();
        $penempatan = SyarikatPenempatan::orderBy('nama_syarikat')->get();
        $isCompanyRole = in_array($role, ['rakan_kolaborasi', 'syarikat_pelaksana']);

        return view('admin.training.index', compact('records', 'penempatan', 'isCompanyRole'));
    }

    public function create()
    {
        $penempatan = SyarikatPenempatan::orderBy('nama_syarikat')->get();
        return view('admin.training.create', compact('penempatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_syarikat' => 'required|exists:syarikat_penempatan,id_syarikat',
            'jenis_training' => 'required|in:Soft Skills,Technical,Safety,Other',
            'tajuk_training' => 'required|string|max:255',
            'sesi' => 'required|in:Session 1,Session 2',
            'tarikh_training' => 'required|date',
            'durasi_jam' => 'required|integer|min:1',
            'lokasi' => 'nullable|string|max:255',
            'trainer_name' => 'required|string|max:200',
            'trainer_type' => 'required|in:Internal,External',
            'jumlah_dijemput' => 'required|integer|min:0',
            'jumlah_hadir' => 'nullable|integer|min:0',
            'topik_covered' => 'nullable|string',
            'pre_assessment_avg' => 'nullable|numeric|min:0|max:10',
            'post_assessment_avg' => 'nullable|numeric|min:0|max:10',
            'skor_kepuasan' => 'nullable|numeric|min:0|max:10',
            'budget_allocated' => 'nullable|numeric|min:0',
            'budget_spent' => 'nullable|numeric|min:0',
            'status' => 'required|in:Dirancang,Dalam Proses,Selesai,Dibatalkan',
            'catatan' => 'nullable|string',
        ]);

        $company = SyarikatPenempatan::find($validated['id_syarikat']);
        $validated['nama_syarikat'] = $company?->nama_syarikat ?? '';
        $validated['id_training'] = TrainingRecord::generateId();
        $validated['jumlah_hadir'] = $validated['jumlah_hadir'] ?? 0;
        $validated['kadar_kehadiran_pct'] = $validated['jumlah_dijemput'] > 0
            ? round(($validated['jumlah_hadir'] / $validated['jumlah_dijemput']) * 100, 2) : 0;

        $pre = $validated['pre_assessment_avg'] ?? 0;
        $post = $validated['post_assessment_avg'] ?? 0;
        $validated['improvement_pct'] = $pre > 0 ? round((($post - $pre) / $pre) * 100, 2) : 0;

        $record = TrainingRecord::create($validated);
        AuditLog::log('training_records', 'create', $record->id_training, null, $record->toArray());

        return redirect()->route('admin.training.show', $record)->with('success', __('messages.training_created'));
    }

    public function show(TrainingRecord $training)
    {
        $this->authorizeCompanyAccess($training);
        $training->load(['syarikatPenempatan', 'participants']);

        $existingIds = $training->participants->pluck('id_graduan')->filter()->toArray();
        $talents = Talent::whereNotNull('id_graduan')
            ->whereNotIn('id_graduan', $existingIds)
            ->orderBy('full_name')
            ->get(['id_graduan', 'full_name', 'id_pelaksana', 'id_syarikat_penempatan']);

        return view('admin.training.show', compact('training', 'talents'));
    }

    public function edit(TrainingRecord $training)
    {
        $this->authorizeCompanyAccess($training);
        $penempatan = SyarikatPenempatan::orderBy('nama_syarikat')->get();
        return view('admin.training.edit', compact('training', 'penempatan'));
    }

    public function update(Request $request, TrainingRecord $training)
    {
        $this->authorizeCompanyAccess($training);
        $validated = $request->validate([
            'id_syarikat' => 'required|exists:syarikat_penempatan,id_syarikat',
            'jenis_training' => 'required|in:Soft Skills,Technical,Safety,Other',
            'tajuk_training' => 'required|string|max:255',
            'sesi' => 'required|in:Session 1,Session 2',
            'tarikh_training' => 'required|date',
            'durasi_jam' => 'required|integer|min:1',
            'lokasi' => 'nullable|string|max:255',
            'trainer_name' => 'required|string|max:200',
            'trainer_type' => 'required|in:Internal,External',
            'jumlah_dijemput' => 'required|integer|min:0',
            'jumlah_hadir' => 'nullable|integer|min:0',
            'topik_covered' => 'nullable|string',
            'pre_assessment_avg' => 'nullable|numeric|min:0|max:10',
            'post_assessment_avg' => 'nullable|numeric|min:0|max:10',
            'skor_kepuasan' => 'nullable|numeric|min:0|max:10',
            'budget_allocated' => 'nullable|numeric|min:0',
            'budget_spent' => 'nullable|numeric|min:0',
            'status' => 'required|in:Dirancang,Dalam Proses,Selesai,Dibatalkan',
            'catatan' => 'nullable|string',
        ]);

        $company = SyarikatPenempatan::find($validated['id_syarikat']);
        $validated['nama_syarikat'] = $company?->nama_syarikat ?? '';
        $validated['jumlah_hadir'] = $validated['jumlah_hadir'] ?? 0;
        $validated['kadar_kehadiran_pct'] = $validated['jumlah_dijemput'] > 0
            ? round(($validated['jumlah_hadir'] / $validated['jumlah_dijemput']) * 100, 2) : 0;

        $pre = $validated['pre_assessment_avg'] ?? 0;
        $post = $validated['post_assessment_avg'] ?? 0;
        $validated['improvement_pct'] = $pre > 0 ? round((($post - $pre) / $pre) * 100, 2) : 0;

        $oldData = $training->toArray();
        $training->update($validated);
        AuditLog::log('training_records', 'update', $training->id_training, $oldData, $training->fresh()->toArray());

        return redirect()->route('admin.training.show', $training)->with('success', __('messages.training_updated'));
    }

    public function addParticipant(Request $request, TrainingRecord $training)
    {
        $request->validate([
            'id_graduan' => 'required|string',
        ]);

        $talent = Talent::where('id_graduan', $request->id_graduan)->first();

        if (!$talent) {
            return back()->with('error', __('messages.talent_not_found'));
        }

        $exists = TrainingParticipant::where('id_training', $training->id_training)
            ->where('id_graduan', $request->id_graduan)
            ->exists();

        if ($exists) {
            return back()->with('error', __('messages.participant_already_exists'));
        }

        TrainingParticipant::create([
            'id_record' => TrainingParticipant::generateId(),
            'id_training' => $training->id_training,
            'id_graduan' => $talent->id_graduan,
            'nama_graduan' => $talent->full_name,
            'status_kehadiran' => 'Hadir',
        ]);

        // Update attendance counts
        $count = TrainingParticipant::where('id_training', $training->id_training)->count();
        $hadir = TrainingParticipant::where('id_training', $training->id_training)
            ->where('status_kehadiran', 'Hadir')->count();
        $training->update([
            'jumlah_dijemput' => $count,
            'jumlah_hadir' => $hadir,
            'kadar_kehadiran_pct' => $count > 0 ? round(($hadir / $count) * 100, 2) : 0,
        ]);

        return back()->with('success', __('messages.participant_added'));
    }

    public function removeParticipant(TrainingRecord $training, TrainingParticipant $participant)
    {
        if ($participant->id_training !== $training->id_training) {
            abort(404);
        }

        $participant->delete();

        $count = TrainingParticipant::where('id_training', $training->id_training)->count();
        $hadir = TrainingParticipant::where('id_training', $training->id_training)
            ->where('status_kehadiran', 'Hadir')->count();
        $training->update([
            'jumlah_dijemput' => $count,
            'jumlah_hadir' => $hadir,
            'kadar_kehadiran_pct' => $count > 0 ? round(($hadir / $count) * 100, 2) : 0,
        ]);

        return back()->with('success', __('messages.participant_removed'));
    }

    private function authorizeCompanyAccess(TrainingRecord $training): void
    {
        $user = auth()->user();
        $role = $user->role?->role_name;

        if ($role === 'rakan_kolaborasi' && $user->id_syarikat_penempatan) {
            if ($training->id_syarikat !== $user->id_syarikat_penempatan) abort(403);
        }
        if ($role === 'syarikat_pelaksana' && $user->id_pelaksana) {
            $companyIds = Talent::where('id_pelaksana', $user->id_pelaksana)->pluck('id_syarikat_penempatan')->filter()->unique()->toArray();
            if (!in_array($training->id_syarikat, $companyIds)) abort(403);
        }
    }
}
