<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\InternshipFeedback;
use App\Models\KehadiranPrestasi;
use App\Models\LogbookUpload;
use App\Models\Placement;
use App\Models\TrainingParticipant;
use App\Models\TrainingRecord;
use Illuminate\Http\Request;

class CompanyController extends BaseMobileController
{
    private function penempatanId(): ?string
    {
        return auth()->user()?->id_syarikat_penempatan;
    }
    public function dashboard()
    {
        $company = $this->resolveCompanyForUser();
        if (!$company) {
            return $this->error('Company profile not found for current user.', 404);
        }

        $placements = $company->placements()->with(['talent.syarikatPelaksana', 'talent.syarikatPenempatan', 'batch', 'company', 'feedback'])->get();
        $sid = $this->penempatanId();
        $activePlacements = $placements->whereIn('placement_status', ['active', 'confirmed']);
        $completedPlacements = $placements->where('placement_status', 'completed');
        $pendingFeedback = $placements
            ->whereIn('placement_status', ['active', 'completed'])
            ->filter(fn($placement) => $placement->feedback->where('feedback_from', 'company')->isEmpty())
            ->count();
        $totalAllocated = $company->budgetAllocations()->sum('allocated_amount');
        $totalDisbursed = $company->budgetTransactions()->where('status', 'approved')->sum('amount');
        $totalPending = $company->budgetTransactions()->where('status', 'pending')->sum('amount');
        $recentPlacements = $placements->sortByDesc('start_date')->take(5)->values();
        $attendanceRecords = $sid
            ? KehadiranPrestasi::with('graduan')
                ->where('id_syarikat', $sid)
                ->orderByDesc('tahun')
                ->orderByDesc('bulan')
                ->orderByDesc('id')
                ->limit(20)
                ->get()
            : collect();
        $logbookRecords = $sid
            ? LogbookUpload::with('graduan')
                ->where('id_syarikat', $sid)
                ->orderByDesc('tarikh_upload')
                ->limit(20)
                ->get()
            : collect();
        $trainingRecords = $sid
            ? TrainingRecord::where('id_syarikat', $sid)
                ->orderByDesc('tarikh_training')
                ->limit(6)
                ->get()
            : collect();
        $avgAttendance = $attendanceRecords->avg('kehadiran_pct');
        $avgPerformance = $attendanceRecords->avg('skor_prestasi');
        $trainingTotal = $trainingRecords->count();
        $trainingCompleted = $trainingRecords
            ->filter(fn($record) => in_array(strtolower((string) $record->status), ['selesai', 'completed'], true))
            ->count();
        $trainingCompliance = $trainingTotal > 0
            ? round(($trainingCompleted / $trainingTotal) * 100, 1)
            : 0;

        return $this->success([
            'company' => [
                'id' => $company->id,
                'company_name' => $company->company_name,
                'company_code' => $company->company_code,
                'industry' => $company->industry,
            ],
            'summary' => [
                'active_placements' => $activePlacements->count(),
                'completed_placements' => $completedPlacements->count(),
                'pending_feedback' => $pendingFeedback,
                'total_allocated' => (float) $totalAllocated,
                'total_disbursed' => (float) $totalDisbursed,
                'total_pending' => (float) $totalPending,
                'remaining_allocation' => (float) max(0, $totalAllocated - $totalDisbursed),
            ],
            'partner_tracking' => [
                'total_graduates' => $placements->pluck('talent.id')->filter()->unique()->count(),
                'average_attendance' => round(((float) $avgAttendance) * 100, 1),
                'average_performance' => round((float) $avgPerformance, 1),
                'training_compliance' => $trainingCompliance,
                'training_completed' => $trainingCompleted,
                'training_total' => $trainingTotal,
            ],
            'recent_attendance' => $attendanceRecords->map(fn($record) => [
                'id' => $record->id,
                'graduate_name' => $record->graduan?->full_name ?? $record->nama_graduan ?? $record->id_graduan,
                'date' => optional($record->tarikh)->toDateString() ?? optional($record->created_at)->toDateString(),
                'status' => $record->status ?? 'hadir',
                'entry_time' => $record->masa_masuk,
            ])->values(),
            'logbook_status' => $logbookRecords->map(fn($record) => [
                'id' => $record->id,
                'graduate_name' => $record->graduan?->full_name ?? $record->nama_graduan ?? $record->id_graduan,
                'month' => $record->minggu ?? $record->week_number ?? $record->bulan,
                'status' => $record->status_semakan ?? $record->status_logbook ?? 'pending',
                'send_date' => $record->tarikh_hantar?->toDateString() ?? $record->tarikh_upload?->toDateString(),
            ])->values(),
            'training_sessions' => $trainingRecords->map(fn($record) => [
                'id' => $record->id,
                'title' => $record->nama_latihan ?? $record->tajuk_training,
                'date' => optional($record->tarikh_mula)->toDateString() ?? optional($record->tarikh_training)->toDateString(),
                'location' => $record->lokasi,
                'trainer' => $record->pengajar ?? $record->trainer_name,
                'status' => $record->status,
            ])->values(),
            'recent_placements' => $recentPlacements
                ->map(fn($placement) => $this->placementPayload($placement))
                ->values(),
        ]);
    }

    public function placements(Request $request)
    {
        $company = $this->resolveCompanyForUser();
        if (!$company) {
            return $this->error('Company profile not found for current user.', 404);
        }

        $query = $company->placements()->with(['talent.syarikatPelaksana', 'talent.syarikatPenempatan', 'batch', 'company']);

        if ($request->filled('status')) {
            $query->where('placement_status', $request->status);
        }

        $placements = $query->orderByDesc('start_date')->get();

        return $this->success([
            'items' => $placements->map(fn($placement) => $this->placementPayload($placement))->values(),
        ]);
    }

    public function showPlacement(Placement $placement)
    {
        $company = $this->resolveCompanyForUser();
        if (!$company || $placement->company_id !== $company->id) {
            return $this->error('Placement not found.', 404);
        }

        $placement->load([
            'talent.documents',
            'talent.certifications',
            'talent.syarikatPelaksana',
            'talent.syarikatPenempatan',
            'batch',
            'company',
            'feedback',
        ]);
        $talent = $placement->talent;
        $transactions = $placement->budgetTransactions()->orderByDesc('transaction_date')->get();
        $companyFeedback = $placement->feedback->firstWhere('feedback_from', 'company');
        $totalDisbursed = $transactions->where('status', 'approved')->sum('amount');
        $totalPending = $transactions->where('status', 'pending')->sum('amount');
        $logbooks = $talent
            ? LogbookUpload::where('id_graduan', $talent->id_graduan ?: $talent->talent_code)
                ->orderByDesc('tahun')
                ->orderByDesc('id')
                ->limit(20)
                ->get()
            : collect();
        $dailyLogs = $talent
            ? $talent->dailyLogs()->orderByDesc('log_date')->limit(20)->get()
            : collect();
        $trainingRecords = $talent
            ? TrainingParticipant::with('trainingRecord')
                ->where('id_graduan', $talent->id_graduan ?: $talent->talent_code)
                ->get()
            : collect();

        return $this->success([
            'placement' => $this->placementPayload($placement),
            'finance' => [
                'total_disbursed' => (float) $totalDisbursed,
                'total_pending' => (float) $totalPending,
                'transactions' => $transactions->map(fn($tx) => [
                    'id' => $tx->id,
                    'category' => $tx->category,
                    'amount' => (float) $tx->amount,
                    'status' => $tx->status,
                    'reference_no' => $tx->reference_no,
                    'transaction_date' => optional($tx->transaction_date)->toDateString(),
                ])->values(),
            ],
            'company_feedback' => $companyFeedback ? [
                'id' => $companyFeedback->id,
                'average_score' => $companyFeedback->average_score,
                'score_technical' => $companyFeedback->score_technical,
                'score_communication' => $companyFeedback->score_communication,
                'score_discipline' => $companyFeedback->score_discipline,
                'score_problem_solving' => $companyFeedback->score_problem_solving,
                'score_professionalism' => $companyFeedback->score_professionalism,
                'comments' => $companyFeedback->comments,
                'submitted_at' => optional($companyFeedback->submitted_at)->toIso8601String(),
            ] : null,
            'logbooks' => $logbooks->map(fn($logbook) => [
                'id' => $logbook->id,
                'month' => $logbook->bulan,
                'year' => $logbook->tahun,
                'logbook_status' => $logbook->status_logbook,
                'review_status' => $logbook->status_semakan,
                'mentor_name' => $logbook->nama_mentor,
                'mentor_comment' => $logbook->komen_mentor,
                'upload_date' => $logbook->tarikh_upload?->toDateString(),
                'file_url' => $logbook->link_file_logbook,
            ])->values(),
            'daily_logs' => $dailyLogs->map(fn($log) => [
                'id' => $log->id,
                'log_date' => optional($log->log_date)->toDateString(),
                'activities' => $log->activities,
                'learnings' => $log->learnings,
                'challenges' => $log->challenges,
                'mood' => $log->mood,
            ])->values(),
            'documents' => $talent
                ? $talent->documents->map(fn($document) => [
                    'id' => $document->id,
                    'type' => $document->document_type,
                    'file_name' => $document->file_name,
                    'file_path' => $document->file_path,
                ])->values()
                : [],
            'certifications' => $talent
                ? $talent->certifications->map(fn($cert) => [
                    'id' => $cert->id,
                    'name' => $cert->certification_name,
                    'issuer' => $cert->issuer,
                    'issue_date' => optional($cert->issue_date)->toDateString(),
                    'expiry_date' => optional($cert->expiry_date)->toDateString(),
                ])->values()
                : [],
            'training_records' => $trainingRecords->map(fn($participant) => [
                'id' => $participant->id,
                'training_title' => $participant->trainingRecord?->tajuk_training,
                'company' => $participant->trainingRecord?->nama_syarikat,
                'session' => $participant->trainingRecord?->sesi,
                'date' => optional($participant->trainingRecord?->tarikh_training)->toDateString(),
                'attendance_pct' => $participant->status_kehadiran === 'Hadir' ? 100 : 0,
                'improvement_pct' => (float) ($participant->improvement_pct ?? 0),
                'status' => $participant->trainingRecord?->status,
                'participant_status' => $participant->status_kehadiran,
            ])->values(),
            'feedback' => $placement->feedback->map(fn($entry) => [
                'id' => $entry->id,
                'feedback_from' => $entry->feedback_from,
                'average_score' => $entry->average_score,
                'comments' => $entry->comments,
                'submitted_at' => optional($entry->submitted_at)->toIso8601String(),
            ])->values(),
        ]);
    }

    public function pendingFeedback()
    {
        $company = $this->resolveCompanyForUser();
        if (!$company) {
            return $this->error('Company profile not found for current user.', 404);
        }

        $placements = $company->placements()
            ->with(['talent', 'batch', 'company'])
            ->whereIn('placement_status', ['active', 'completed'])
            ->whereDoesntHave('feedback', fn($q) => $q->where('feedback_from', 'company'))
            ->orderByDesc('start_date')
            ->get();

        return $this->success([
            'items' => $placements->map(fn($placement) => $this->placementPayload($placement))->values(),
        ]);
    }

    public function storeFeedback(Request $request)
    {
        $company = $this->resolveCompanyForUser();
        if (!$company) {
            return $this->error('Company profile not found for current user.', 404);
        }

        $validated = $request->validate([
            'placement_id' => 'required|exists:placements,id',
            'score_technical' => 'nullable|integer|min:1|max:5',
            'score_communication' => 'nullable|integer|min:1|max:5',
            'score_discipline' => 'nullable|integer|min:1|max:5',
            'score_problem_solving' => 'nullable|integer|min:1|max:5',
            'score_professionalism' => 'nullable|integer|min:1|max:5',
            'comments' => 'nullable|string',
        ]);

        if (
            !isset($validated['score_technical']) &&
            !isset($validated['score_communication']) &&
            !isset($validated['score_discipline']) &&
            !isset($validated['score_problem_solving']) &&
            !isset($validated['score_professionalism']) &&
            blank($validated['comments'] ?? null)
        ) {
            return $this->error('At least one score or comment is required.', 422, [
                'feedback' => ['At least one score or comment is required.'],
            ]);
        }

        $placement = Placement::with(['talent', 'company'])->findOrFail($validated['placement_id']);
        if ($placement->company_id !== $company->id) {
            return $this->error('Placement not found.', 404);
        }

        if ($placement->feedback()->where('feedback_from', 'company')->exists()) {
            return $this->error('Feedback has already been submitted for this placement.', 422, [
                'placement_id' => ['Feedback has already been submitted for this placement.'],
            ]);
        }

        $feedback = InternshipFeedback::create([
            'placement_id' => $placement->id,
            'feedback_from' => 'company',
            'score_technical' => $validated['score_technical'] ?? null,
            'score_communication' => $validated['score_communication'] ?? null,
            'score_discipline' => $validated['score_discipline'] ?? null,
            'score_problem_solving' => $validated['score_problem_solving'] ?? null,
            'score_professionalism' => $validated['score_professionalism'] ?? null,
            'comments' => $validated['comments'] ?? null,
            'submitted_at' => now(),
        ]);

        return $this->success([
            'feedback' => [
                'id' => $feedback->id,
                'feedback_from' => $feedback->feedback_from,
                'average_score' => $feedback->average_score,
                'submitted_at' => optional($feedback->submitted_at)->toIso8601String(),
            ],
        ], 'Feedback submitted successfully.', 201);
    }

    // ── Rakan Kolaborasi PROTEGE endpoints ──────────────────────────

    public function attendance(Request $request)
    {
        $sid = $this->penempatanId();
        if (!$sid) {
            return $this->error('Company profile not found.', 404);
        }

        $query = KehadiranPrestasi::with('graduan')->where('id_syarikat', $sid);

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('id_graduan')) {
            $query->where('id_graduan', $request->id_graduan);
        }

        $records = $query->orderByDesc('tahun')->orderByDesc('id')->paginate(20);

        return $this->success([
            'items' => $records->map(fn($r) => [
                'id' => $r->id,
                'id_graduan' => $r->id_graduan,
                'graduate_name' => $r->graduan?->full_name ?? $r->nama_graduan ?? $r->id_graduan,
                'month' => $r->bulan,
                'year' => $r->tahun,
                'attendance_pct' => round($r->kehadiran_pct * 100, 1),
                'attendance_level' => $r->kehadiran_level,
                'days_present' => $r->hari_hadir,
                'working_days' => $r->hari_bekerja,
                'performance_score' => $r->skor_prestasi,
                'performance_level' => $r->prestasi_level,
                'mentor_comment' => $r->komen_mentor,
                'logbook_status' => $r->status_logbook,
                'status' => $r->status ?? 'hadir',
                'date' => optional($r->tarikh)->toDateString() ?? optional($r->created_at)->toDateString(),
                'entry_time' => $r->masa_masuk,
            ])->values(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function logbooks(Request $request)
    {
        $sid = $this->penempatanId();
        if (!$sid) {
            return $this->error('Company profile not found.', 404);
        }

        $query = LogbookUpload::where('id_syarikat', $sid);

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('status_logbook')) {
            $query->where('status_logbook', $request->status_logbook);
        }
        if ($request->filled('status_semakan')) {
            $query->where('status_semakan', $request->status_semakan);
        }

        $records = $query->orderByDesc('tarikh_upload')->paginate(20);

        return $this->success([
            'items' => $records->map(fn($r) => [
                'id' => $r->id,
                'id_graduan' => $r->id_graduan,
                'graduate_name' => $r->nama_graduan,
                'month' => $r->bulan,
                'year' => $r->tahun,
                'logbook_status' => $r->status_logbook,
                'upload_date' => $r->tarikh_upload?->toDateString(),
                'review_status' => $r->status_semakan,
                'mentor_comment' => $r->komen_mentor,
                'review_date' => $r->tarikh_semakan?->toDateString(),
                'mentor_name' => $r->nama_mentor,
                'file_url' => $r->link_file_logbook,
            ])->values(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function reviewLogbook(Request $request, $id)
    {
        $sid = $this->penempatanId();
        if (!$sid) {
            return $this->error('Company profile not found.', 404);
        }

        $logbook = LogbookUpload::where('id_syarikat', $sid)->findOrFail($id);

        $validated = $request->validate([
            'status_semakan' => 'required|in:Lulus,Dalam Proses,Perlu Semakan Semula,Belum Disemak',
            'komen_mentor' => 'nullable|string|max:1000',
        ]);

        $logbook->update([
            'status_semakan' => $validated['status_semakan'],
            'komen_mentor' => $validated['komen_mentor'] ?? $logbook->komen_mentor,
            'tarikh_semakan' => now(),
            'nama_mentor' => auth()->user()?->full_name,
        ]);

        return $this->success([
            'id' => $logbook->id,
            'review_status' => $logbook->status_semakan,
            'review_date' => $logbook->tarikh_semakan?->toDateString(),
        ], 'Logbook reviewed successfully.');
    }

    public function training(Request $request)
    {
        $sid = $this->penempatanId();
        if (!$sid) {
            return $this->error('Company profile not found.', 404);
        }

        $query = TrainingRecord::where('id_syarikat', $sid);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderByDesc('tarikh_training')->paginate(20);

        return $this->success([
            'items' => $records->map(fn($r) => [
                'id' => $r->id,
                'id_training' => $r->id_training,
                'title' => $r->tajuk_training,
                'type' => $r->jenis_training,
                'session' => $r->sesi,
                'date' => $r->tarikh_training?->toDateString(),
                'duration_hours' => $r->durasi_jam,
                'location' => $r->lokasi,
                'trainer' => $r->trainer_name,
                'invited' => $r->jumlah_dijemput,
                'attended' => $r->jumlah_hadir,
                'attendance_pct' => $r->jumlah_dijemput > 0 ? round(($r->jumlah_hadir / $r->jumlah_dijemput) * 100) : 0,
                'pre_avg' => (float) $r->pre_assessment_avg,
                'post_avg' => (float) $r->post_assessment_avg,
                'improvement_pct' => (float) $r->improvement_pct,
                'status' => $r->status,
            ])->values(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'total' => $records->total(),
            ],
        ]);
    }
}
