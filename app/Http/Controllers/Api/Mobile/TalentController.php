<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\BudgetTransaction;
use App\Models\DailyLog;
use App\Models\Role;
use App\Models\Talent;
use App\Models\TalentDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TalentController extends BaseMobileController
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'ic_passport_no' => 'required|string|max:20|unique:talents,ic_passport_no',
            'gender' => 'required|in:Lelaki,Perempuan',
            'date_of_birth' => 'required|date',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'email' => 'required|email|unique:talents,email|unique:users,email',
            'background_type' => 'required|in:anak_atm,anak_veteran_atm,anak_awam_mindef',
            'guardian_name' => 'required|string|max:255',
            'guardian_ic' => 'required|string|max:20',
            'guardian_relationship' => 'required|string|max:100',
            'highest_qualification' => 'required|string',
            'university' => 'required|string|max:255',
            'programme' => 'required|string|max:255',
            'graduation_year' => 'required|integer|min:1990|max:' . (date('Y') + 2),
            'cgpa' => 'nullable|numeric|min:0|max:4',
            'preferred_sectors' => 'required|array|min:1',
            'preferred_locations' => 'required|array|min:1',
            'currently_employed' => 'required|boolean',
            'available_start_date' => 'nullable|date',
            'resume' => 'required|file|mimes:pdf|max:10240',
            'ic_copy' => 'required|file|mimes:pdf|max:10240',
            'transcript' => 'required|file|mimes:pdf|max:10240',
            'military_card' => 'nullable|file|mimes:pdf|max:10240',
            'pdpa_consent' => 'required|accepted',
            'declaration_signature' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $role = Role::where('role_name', 'talent')->first();
        if (!$role) {
            return $this->error('Talent role is not configured.', 500);
        }

        $created = DB::transaction(function () use ($validated, $role, $request) {
            $talent = Talent::create([
                'talent_code' => Talent::generateCode(),
                'full_name' => $validated['full_name'],
                'ic_passport_no' => $validated['ic_passport_no'],
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'email' => $validated['email'],
                'university' => $validated['university'],
                'programme' => $validated['programme'],
                'graduation_year' => $validated['graduation_year'],
                'cgpa' => $validated['cgpa'] ?? null,
                'status' => 'applied',
                'public_visibility' => false,
                'background_type' => $validated['background_type'],
                'guardian_name' => $validated['guardian_name'],
                'guardian_ic' => $validated['guardian_ic'],
                'guardian_military_no' => $request->guardian_military_no,
                'guardian_relationship' => $validated['guardian_relationship'],
                'highest_qualification' => $validated['highest_qualification'],
                'preferred_sectors' => $validated['preferred_sectors'],
                'preferred_locations' => $validated['preferred_locations'],
                'currently_employed' => $validated['currently_employed'],
                'available_start_date' => $validated['available_start_date'] ?? null,
                'pdpa_consent' => true,
                'declaration_signature' => $validated['declaration_signature'],
            ]);

            $user = User::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $role->id,
                'talent_id' => $talent->id,
                'status' => 'active',
                'language' => app()->getLocale(),
            ]);

            foreach ([
                'resume' => 'resume',
                'ic_copy' => 'ic_copy',
                'transcript' => 'transcript',
                'military_card' => 'military_card',
            ] as $field => $type) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = $type . '_' . $talent->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/' . $talent->id, $filename, 'public');

                    TalentDocument::create([
                        'talent_id' => $talent->id,
                        'document_type' => $type,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'uploaded_at' => now(),
                    ]);
                }
            }

            return [$talent, $user];
        });

        [$talent, $user] = $created;

        return $this->success([
            'talent' => $this->talentPayload($talent),
            'user' => $this->userPayload($user->load('role')),
        ], 'Registration submitted successfully.', 201);
    }

    public function profile()
    {
        $talent = $this->resolveTalentForUser();
        if (!$talent) {
            return $this->error('Talent profile not found for current user.', 404);
        }

        return $this->success([
            'talent' => $this->talentPayload($talent),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $talent = $this->resolveTalentForUser();
        if (!$talent) {
            return $this->error('Talent profile not found for current user.', 404);
        }

        $validated = $request->validate([
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'university' => 'nullable|string|max:200',
            'programme' => 'nullable|string|max:200',
            'cgpa' => 'nullable|numeric|min:0|max:4',
            'graduation_year' => 'nullable|integer|digits:4|min:1900|max:2100',
            'skills_text' => 'nullable|string',
            'profile_summary' => 'nullable|string',
            'preferred_sectors' => 'nullable|array',
            'preferred_locations' => 'nullable|array',
            'currently_employed' => 'nullable|boolean',
            'available_start_date' => 'nullable|date',
            'public_visibility' => 'nullable|boolean',
        ]);

        $talent->update($validated);

        return $this->success([
            'talent' => $this->talentPayload($talent->fresh()),
        ], 'Profile updated successfully.');
    }

    public function applicationStatus()
    {
        $talent = $this->resolveTalentForUser();
        if (!$talent) {
            return $this->error('Talent profile not found for current user.', 404);
        }

        $currentPlacement = $talent->placements()->with(['company', 'batch'])
            ->whereIn('placement_status', ['planned', 'confirmed', 'active', 'extended'])
            ->latest('start_date')
            ->first();

        return $this->success([
            'application' => [
                'talent_code' => $talent->talent_code,
                'status' => $talent->status,
                'reviewed_at' => optional($talent->reviewed_at)->toIso8601String(),
                'rejection_reason' => $talent->rejection_reason,
                'current_placement' => $currentPlacement ? $this->placementPayload($currentPlacement) : null,
            ],
        ]);
    }

    public function documents()
    {
        $talent = $this->resolveTalentForUser();
        if (!$talent) {
            return $this->error('Talent profile not found for current user.', 404);
        }

        $documents = $talent->documents()->orderByDesc('uploaded_at')->get();

        return $this->success([
            'items' => $documents->map(function (TalentDocument $document) {
                return [
                    'id' => $document->id,
                    'document_type' => $document->document_type,
                    'file_name' => $document->file_name,
                    'file_url' => Storage::disk('public')->url($document->file_path),
                    'uploaded_at' => optional($document->uploaded_at)->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function storeDocument(Request $request)
    {
        $talent = $this->resolveTalentForUser();
        if (!$talent) {
            return $this->error('Talent profile not found for current user.', 404);
        }

        $validated = $request->validate([
            'document_type' => 'required|string|max:50',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $file = $request->file('file');
        $filename = $validated['document_type'] . '_' . $talent->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents/' . $talent->id, $filename, 'public');

        $document = TalentDocument::create([
            'talent_id' => $talent->id,
            'document_type' => $validated['document_type'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'uploaded_at' => now(),
        ]);

        return $this->success([
            'document' => [
                'id' => $document->id,
                'document_type' => $document->document_type,
                'file_name' => $document->file_name,
                'file_url' => Storage::disk('public')->url($document->file_path),
                'uploaded_at' => optional($document->uploaded_at)->toIso8601String(),
            ],
        ], 'Document uploaded successfully.', 201);
    }

    public function deleteDocument(TalentDocument $document)
    {
        $talent = $this->resolveTalentForUser();
        if (!$talent || $document->talent_id !== $talent->id) {
            return $this->error('Document not found.', 404);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return $this->success([], 'Document deleted successfully.');
    }

    public function currentPlacement()
    {
        $talent = $this->resolveTalentForUser();
        if (!$talent) {
            return $this->error('Talent profile not found for current user.', 404);
        }

        $placement = $talent->placements()
            ->with(['company', 'batch', 'talent'])
            ->whereIn('placement_status', ['planned', 'confirmed', 'active', 'extended'])
            ->latest('start_date')
            ->first();

        return $this->success([
            'placement' => $placement ? $this->placementPayload($placement) : null,
        ]);
    }

    public function placementHistory()
    {
        $talent = $this->resolveTalentForUser();
        if (!$talent) {
            return $this->error('Talent profile not found for current user.', 404);
        }

        $placements = $talent->placements()->with(['company', 'batch', 'talent'])->orderByDesc('start_date')->get();

        return $this->success([
            'items' => $placements->map(fn($placement) => $this->placementPayload($placement))->values(),
        ]);
    }

    public function feedback()
    {
        $talent = $this->resolveTalentForUser();
        if (!$talent) {
            return $this->error('Talent profile not found for current user.', 404);
        }

        $feedback = $talent->placements()
            ->with(['feedback', 'company'])
            ->get()
            ->flatMap(function ($placement) {
                return $placement->feedback->map(function ($entry) use ($placement) {
                    return [
                        'id' => $entry->id,
                        'placement_id' => $placement->id,
                        'company_name' => $placement->company?->company_name,
                        'feedback_from' => $entry->feedback_from,
                        'average_score' => $entry->average_score,
                        'score_technical' => $entry->score_technical,
                        'score_communication' => $entry->score_communication,
                        'score_discipline' => $entry->score_discipline,
                        'score_problem_solving' => $entry->score_problem_solving,
                        'score_professionalism' => $entry->score_professionalism,
                        'comments' => $entry->comments,
                        'submitted_at' => optional($entry->submitted_at)->toIso8601String(),
                    ];
                });
            })->values();

        return $this->success(['items' => $feedback->all()]);
    }

    public function dailyLogs(Request $request)
    {
        $talent = $this->resolveTalentForUser();
        if (!$talent) {
            return $this->error('Talent profile not found for current user.', 404);
        }

        $query = $talent->dailyLogs()
            ->with(['placement.company'])
            ->orderByDesc('log_date')
            ->orderByDesc('id');

        if ($request->filled('month')) {
            $monthValue = (string) $request->month;

            if (!preg_match('/^\d{4}-\d{2}$/', $monthValue)) {
                return $this->error('Invalid month format. Expected YYYY-MM.', 422, [
                    'month' => ['Invalid month format. Expected YYYY-MM.'],
                ]);
            }

            [$year, $month] = explode('-', $monthValue);
            $query->whereYear('log_date', (int) $year)
                ->whereMonth('log_date', (int) $month);
        }

        return $this->success([
            'items' => $query->get()->map(fn(DailyLog $dailyLog) => $this->dailyLogPayload($dailyLog))->values()->all(),
        ]);
    }

    public function showDailyLog(DailyLog $dailyLog)
    {
        $talent = $this->resolveTalentForUser();
        if (!$talent) {
            return $this->error('Talent profile not found for current user.', 404);
        }

        $dailyLog->loadMissing(['placement.company']);

        if ((int) $dailyLog->talent_id !== (int) $talent->id) {
            return $this->error('Daily log not found.', 404);
        }

        return $this->success([
            'log' => $this->dailyLogPayload($dailyLog),
        ]);
    }

    public function storeDailyLog(Request $request)
    {
        $talent = $this->resolveTalentForUser();
        if (!$talent) {
            return $this->error('Talent profile not found for current user.', 404);
        }

        $validated = $request->validate([
            'log_date' => 'required|date|before_or_equal:today',
            'activities' => 'required|string|max:3000',
            'challenges' => 'nullable|string|max:1500',
            'learnings' => 'nullable|string|max:1500',
            'mood' => 'required|in:great,good,okay,bad,neutral,tired,difficult',
            'status' => 'nullable|in:draft,submitted',
        ]);

        $logDate = date('Y-m-d', strtotime($validated['log_date']));
        $exists = $talent->dailyLogs()->whereDate('log_date', $logDate)->exists();

        if ($exists) {
            return $this->error('A daily log already exists for this date.', 422, [
                'log_date' => ['A daily log already exists for this date.'],
            ]);
        }

        $activePlacement = $talent->placements()
            ->whereIn('placement_status', ['active', 'confirmed'])
            ->first();

        $dailyLog = $talent->dailyLogs()->create([
            'placement_id' => $activePlacement?->id,
            'log_date' => $logDate,
            'activities' => $validated['activities'],
            'challenges' => $validated['challenges'] ?? null,
            'learnings' => $validated['learnings'] ?? null,
            'mood' => $validated['mood'],
            'status' => $validated['status'] ?? 'submitted',
        ]);

        $dailyLog->loadMissing(['placement.company']);

        return $this->success([
            'log' => $this->dailyLogPayload($dailyLog),
        ], 'Daily log saved successfully.', 201);
    }

    public function allowance()
    {
        $talent = $this->resolveTalentForUser();
        if (!$talent) {
            return $this->error('Talent profile not found for current user.', 404);
        }

        // Current active placement for monthly stipend
        $placement = $talent->placements()
            ->with(['company'])
            ->whereIn('placement_status', ['planned', 'confirmed', 'active', 'extended'])
            ->latest('start_date')
            ->first();

        $transactions = BudgetTransaction::where('talent_id', $talent->id)
            ->orderByDesc('transaction_date')
            ->get();

        $approved = $transactions->where('status', 'approved')->sum('amount');
        $pending  = $transactions->where('status', 'pending')->sum('amount');

        return $this->success([
            'monthly_stipend'  => $placement ? (float) $placement->monthly_stipend : null,
            'placement_status' => $placement?->placement_status,
            'company_name'     => $placement?->company?->company_name,
            'total_approved'   => (float) $approved,
            'total_pending'    => (float) $pending,
            'transactions'     => $transactions->map(fn($t) => [
                'id'               => $t->id,
                'transaction_date' => optional($t->transaction_date)->toDateString(),
                'category'         => $t->category,
                'description'      => $t->description,
                'amount'           => (float) $t->amount,
                'status'           => $t->status,
                'reference_no'     => $t->reference_no,
            ])->values()->all(),
        ]);
    }

    private function normalizeMobileMood(string $mood): string
    {
        return match ($mood) {
            'okay' => 'neutral',
            'bad' => 'difficult',
            default => $mood,
        };
    }

    /**
     * Attendance & Performance summary for the talent.
     */
    public function attendanceSummary()
    {
        $talent = $this->resolveTalentForUser();
        $gradId = $talent->id_graduan ?? $talent->talent_code;

        $records = \App\Models\KehadiranPrestasi::where('id_graduan', $gradId)
            ->orderByDesc('tahun')->orderByDesc('id')
            ->limit(12)
            ->get();

        $avg_kehadiran = $records->avg('kehadiran_pct');
        $avg_prestasi = $records->avg('skor_prestasi');
        $latest = $records->first();

        return $this->success([
            'average_attendance_pct' => $avg_kehadiran ? round($avg_kehadiran * 100, 1) : null,
            'average_performance_score' => $avg_prestasi ? round($avg_prestasi, 1) : null,
            'latest_month' => $latest?->bulan,
            'latest_attendance_pct' => $latest ? round($latest->kehadiran_pct * 100, 1) : null,
            'latest_performance_score' => $latest?->skor_prestasi,
            'low_attendance_alert' => $avg_kehadiran !== null && $avg_kehadiran < 0.75,
            'low_performance_alert' => $avg_prestasi !== null && $avg_prestasi < 6,
            'records' => $records->map(fn($r) => [
                'month' => $r->bulan,
                'year' => $r->tahun,
                'attendance_pct' => round($r->kehadiran_pct * 100, 1),
                'days_present' => $r->hari_hadir,
                'days_working' => $r->hari_bekerja,
                'performance_score' => $r->skor_prestasi,
                'logbook_status' => $r->status_logbook,
                'mentor_comment' => $r->komen_mentor,
            ])->values(),
        ]);
    }

    /**
     * Letter workflow status for the talent.
     */
    public function letterStatus()
    {
        $talent = $this->resolveTalentForUser();
        $gradId = $talent->id_graduan ?? $talent->talent_code;

        $records = \App\Models\StatusSurat::where('id_graduan', $gradId)
            ->orderByDesc('created_at')
            ->get();

        return $this->success([
            'items' => $records->map(fn($s) => [
                'id' => $s->id,
                'type' => $s->jenis_surat,
                'status' => $s->status_surat,
                'pic' => $s->pic_responsible,
                'start_date' => $s->tarikh_mula_proses?->toDateString(),
                'completed_date' => $s->tarikh_siap?->toDateString(),
                'has_attachment' => !empty($s->file_attachment),
                'issue' => $s->isu_halangan,
            ])->values(),
        ]);
    }

    /**
     * Training participation for the talent.
     */
    public function training()
    {
        $talent = $this->resolveTalentForUser();
        $gradId = $talent->id_graduan ?? $talent->talent_code;

        $participations = \App\Models\TrainingParticipant::where('id_graduan', $gradId)
            ->with('trainingRecord')
            ->get();

        return $this->success([
            'items' => $participations->map(fn($tp) => [
                'id' => $tp->id,
                'training_title' => $tp->trainingRecord?->tajuk_training,
                'training_type' => $tp->trainingRecord?->jenis_training,
                'session' => $tp->trainingRecord?->sesi,
                'date' => $tp->trainingRecord?->tarikh_training?->toDateString(),
                'attendance_status' => $tp->status_kehadiran,
                'pre_score' => $tp->pre_assessment_score,
                'post_score' => $tp->post_assessment_score,
                'improvement_pct' => $tp->improvement_pct,
                'certificate_issued' => $tp->certificate_issued,
                'status' => $tp->trainingRecord?->status,
            ])->values(),
        ]);
    }

    /**
     * Issues and alerts for the talent.
     */
    public function issuesAndAlerts()
    {
        $talent = $this->resolveTalentForUser();
        $gradId = $talent->id_graduan ?? $talent->talent_code;

        // Payment alerts
        $latePayments = \App\Models\KewanganElaun::where('id_graduan', $gradId)
            ->where('status_bayaran', 'Lewat')
            ->where('hari_lewat', '>', 7)
            ->count();

        // Attendance alert
        $lowAttendance = \App\Models\KehadiranPrestasi::where('id_graduan', $gradId)
            ->where('kehadiran_pct', '<', 0.75)
            ->orderByDesc('id')
            ->exists();

        // Surat delay
        $suratDelay = \App\Models\StatusSurat::where('id_graduan', $gradId)
            ->where('status_surat', '!=', 'Selesai')
            ->whereNotNull('tarikh_mula_proses')
            ->where('tarikh_mula_proses', '<=', now()->subDays(14))
            ->count();

        // Linked issues
        $issues = \App\Models\IsuRisiko::where(function ($q) use ($talent) {
            $q->where('butiran_isu', 'like', '%' . ($talent->id_graduan ?? '') . '%');
        })->whereIn('status', ['Baru', 'Dalam Tindakan'])
            ->orderByDesc('tarikh_isu')
            ->limit(10)
            ->get();

        return $this->success([
            'alerts' => [
                'late_payments' => $latePayments,
                'low_attendance' => $lowAttendance,
                'surat_delayed' => $suratDelay,
            ],
            'issues' => $issues->map(fn($i) => [
                'id' => $i->id_isu,
                'category' => $i->kategori_isu,
                'severity' => $i->tahap_risiko,
                'status' => $i->status,
                'description' => \Illuminate\Support\Str::limit($i->butiran_isu, 100),
                'pic' => $i->pic,
                'date' => $i->tarikh_isu?->toDateString(),
            ])->values(),
        ]);
    }

    private function dailyLogPayload(DailyLog $dailyLog): array
    {
        return [
            'id' => $dailyLog->id,
            'talent_id' => $dailyLog->talent_id,
            'placement_id' => $dailyLog->placement_id,
            'log_date' => optional($dailyLog->log_date)->toDateString(),
            'activities' => $dailyLog->activities,
            'challenges' => $dailyLog->challenges,
            'learnings' => $dailyLog->learnings,
            'mood' => $dailyLog->mood,
            'status' => $dailyLog->status,
            'reviewed_at' => optional($dailyLog->reviewed_at)->toIso8601String(),
            'reviewed_by' => $dailyLog->reviewed_by,
            'admin_remarks' => $dailyLog->admin_remarks,
            'placement' => $dailyLog->relationLoaded('placement') && $dailyLog->placement ? [
                'id' => $dailyLog->placement->id,
                'department' => $dailyLog->placement->department,
                'placement_status' => $dailyLog->placement->placement_status,
                'company_name' => $dailyLog->placement->relationLoaded('company') && $dailyLog->placement->company
                    ? $dailyLog->placement->company->company_name
                    : null,
            ] : null,
            'created_at' => optional($dailyLog->created_at)->toIso8601String(),
            'updated_at' => optional($dailyLog->updated_at)->toIso8601String(),
        ];
    }
}
