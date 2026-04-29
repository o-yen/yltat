<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\AuditLog;
use App\Models\BudgetAllocation;
use App\Models\BudgetTransaction;
use App\Models\Company;
use App\Models\InternshipFeedback;
use App\Models\IsuRisiko;
use App\Models\KehadiranPrestasi;
use App\Models\KewanganElaun;
use App\Models\KpiDashboard;
use App\Models\DailyLog;
use App\Models\LogbookUpload;
use App\Models\Placement;
use App\Models\StatusSurat;
use App\Models\SyarikatPelaksana;
use App\Models\SyarikatPenempatan;
use App\Models\Talent;
use App\Models\TrainingParticipant;
use App\Models\TrainingRecord;
use App\Models\IntakeBatch;
use App\Models\Role;
use App\Models\User;
use App\Services\DataMaskingService;
use App\Services\MobileNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends BaseMobileController
{
    public function __construct(private MobileNotificationService $notifications)
    {
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->get('per_page', 20), 1), 100);
    }

    public function dashboard()
    {
        $currentYear = date('Y');

        // Active graduates
        $totalGraduanAktif = Talent::where(function ($q) {
            $q->where('status_aktif', 'Aktif')
              ->orWhere(function ($sq) {
                  $sq->whereNull('status_aktif')->where('status', 'Aktif');
              });
        })->count();

        $totalGraduan = Talent::whereNotNull('id_graduan')->count();
        $pendingApplications = Talent::whereNotNull('id_graduan')->where('status', 'applied')->count();

        // Surat completion
        $suratKuningTotal = StatusSurat::where('jenis_surat', 'Surat Kuning')->count();
        $suratKuningSelesai = StatusSurat::where('jenis_surat', 'Surat Kuning')->where('status_surat', 'Selesai')->count();
        $suratBiruTotal = StatusSurat::where('jenis_surat', 'Surat Biru')->count();
        $suratBiruSelesai = StatusSurat::where('jenis_surat', 'Surat Biru')->where('status_surat', 'Selesai')->count();

        // Issues
        $kritikalCount = IsuRisiko::where('tahap_risiko', 'Kritikal')->whereIn('status', ['Baru', 'Dalam Tindakan'])->count();
        $activeIssuesCount = IsuRisiko::whereIn('status', ['Baru', 'Dalam Tindakan'])->count();

        // Payments
        $bayaranSelesai = KewanganElaun::where('status_bayaran', 'Selesai')->count();
        $bayaranLewat = KewanganElaun::where('status_bayaran', 'Lewat')->count();

        // Budget
        $totalAllocated = (float) BudgetAllocation::where('fiscal_year', $currentYear)->sum('allocated_amount');
        $totalSpent = (float) BudgetTransaction::where('status', 'approved')->whereYear('transaction_date', $currentYear)->sum('amount');

        // Graduates per placement company (top 10)
        $graduanPerPenempatan = SyarikatPenempatan::select('nama_syarikat', 'jumlah_graduan_ditempatkan')
            ->orderByDesc('jumlah_graduan_ditempatkan')
            ->limit(10)
            ->get()
            ->map(fn($sp) => [
                'label' => $sp->nama_syarikat,
                'value' => (int) $sp->jumlah_graduan_ditempatkan,
            ])->values();

        // Sector distribution
        $sektorData = Talent::with('syarikatPenempatan:id_syarikat,sektor_industri')
            ->whereNotNull('id_graduan')
            ->whereNotNull('id_syarikat_penempatan')
            ->get(['id_graduan', 'id_syarikat_penempatan'])
            ->groupBy(fn($t) => trim((string) data_get($t, 'syarikatPenempatan.sektor_industri')) ?: 'Tidak Ditetapkan')
            ->map(fn($talents, $sector) => [
                'label' => $sector,
                'value' => $talents->pluck('id_graduan')->filter()->unique()->count(),
            ])
            ->sortByDesc('value')
            ->values();
        $sektorTotal = $sektorData->sum('value') ?: 1;
        $sektorWithPct = $sektorData->map(fn($s) => [
            'label' => $s['label'],
            'value' => $s['value'],
            'pct' => round($s['value'] / $sektorTotal * 100, 1),
        ])->values();

        // Active issues list (recent 5)
        $recentIssues = IsuRisiko::with('syarikatPelaksana')
            ->whereIn('status', ['Baru', 'Dalam Tindakan'])
            ->orderByDesc('tarikh_isu')
            ->limit(5)
            ->get()
            ->map(fn($i) => [
                'title' => $i->kategori_isu,
                'desc' => \Illuminate\Support\Str::limit($i->butiran_isu, 80),
                'level' => $i->tahap_risiko,
                'status' => $i->status,
                'date' => $i->tarikh_isu?->toDateString(),
                'pelaksana' => $i->syarikatPelaksana?->nama_syarikat,
            ])->values();

        return $this->success([
            'summary' => [
                'total_graduan' => $totalGraduan,
                'total_graduan_aktif' => $totalGraduanAktif,
                'pending_applications' => $pendingApplications,
                'surat_kuning_selesai' => $suratKuningSelesai,
                'surat_kuning_total' => $suratKuningTotal,
                'surat_biru_selesai' => $suratBiruSelesai,
                'surat_biru_total' => $suratBiruTotal,
                'isu_kritikal' => $kritikalCount,
                'isu_aktif' => $activeIssuesCount,
                'bayaran_selesai' => $bayaranSelesai,
                'bayaran_lewat' => $bayaranLewat,
                'allocated_budget' => $totalAllocated,
                'spent_budget' => $totalSpent,
            ],
            'graduan_mengikut_syarikat_penempatan' => $graduanPerPenempatan,
            'sektor_industri' => $sektorWithPct,
            'amaran_notifikasi' => $recentIssues,
        ]);
    }

    public function applications(Request $request)
    {
        $query = Talent::query()->whereNotNull('id_graduan');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('ic_passport_no', 'like', "%{$search}%");
            });
        }

        $talents = $query->orderByDesc('created_at')->paginate($this->perPage($request));

        return $this->success([
            'items' => $talents->getCollection()->map(function (Talent $talent) {
                return [
                    'id' => $talent->id,
                    'talent_code' => $talent->talent_code,
                    'full_name' => $talent->full_name,
                    'ic_passport_no' => $talent->ic_passport_no,
                    'email' => $talent->email,
                    'status' => $talent->status,
                    'highest_qualification' => $talent->highest_qualification,
                    'graduation_year' => $talent->graduation_year,
                    'created_at' => optional($talent->created_at)->toIso8601String(),
                ];
            })->values(),
            'pagination' => [
                'current_page' => $talents->currentPage(),
                'last_page' => $talents->lastPage(),
                'per_page' => $talents->perPage(),
                'total' => $talents->total(),
            ],
        ]);
    }

    public function showApplication(Talent $talent)
    {
        abort_unless($talent->id_graduan, 404);

        $talent->load(['documents', 'placements.company', 'placements.batch']);

        return $this->success([
            'talent' => $this->talentPayload($talent),
            'documents' => $talent->documents->map(fn($document) => [
                'id' => $document->id,
                'document_type' => $document->document_type,
                'file_name' => $document->file_name,
                'file_url' => \Storage::disk('public')->url($document->file_path),
            ])->values(),
            'placements' => $talent->placements->map(fn($placement) => $this->placementPayload($placement))->values(),
        ]);
    }

    public function approveApplication(Talent $talent)
    {
        abort_unless($talent->id_graduan, 404);

        if (!in_array($talent->status, ['applied', 'shortlisted'], true)) {
            return $this->error('Application cannot be approved from its current status.', 422, [
                'status' => ['Application cannot be approved from its current status.'],
            ]);
        }

        $oldData = $talent->toArray();

        $talent->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        AuditLog::log('applications', 'approve', $talent->id, $oldData, $talent->fresh()->toArray());

        $this->notifications->notifyUserByEmail(
            $talent->email,
            'Application approved',
            'Your Protege MINDEF application has been approved.',
            'application.approved',
            ['talent_id' => $talent->id]
        );

        return $this->success([
            'talent' => $this->talentPayload($talent->fresh()),
        ], 'Application approved successfully.');
    }

    public function rejectApplication(Request $request, Talent $talent)
    {
        abort_unless($talent->id_graduan, 404);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if (!in_array($talent->status, ['applied', 'shortlisted'], true)) {
            return $this->error('Application cannot be rejected from its current status.', 422, [
                'status' => ['Application cannot be rejected from its current status.'],
            ]);
        }

        $oldData = $talent->toArray();

        $talent->update([
            'status' => 'inactive',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        AuditLog::log('applications', 'reject', $talent->id, $oldData, $talent->fresh()->toArray());

        $this->notifications->notifyUserByEmail(
            $talent->email,
            'Application rejected',
            'Your Protege MINDEF application was not approved.',
            'application.rejected',
            ['talent_id' => $talent->id]
        );

        return $this->success([
            'talent' => $this->talentPayload($talent->fresh()),
        ], 'Application rejected successfully.');
    }

    public function placements(Request $request)
    {
        $query = Placement::with(['talent', 'company', 'batch'])
            ->whereHas('talent', fn ($talentQuery) => $talentQuery->whereNotNull('id_graduan'))
            ->whereHas('company', fn ($companyQuery) => $companyQuery->where('company_code', 'like', 'SPTAN_%'));

        if ($request->filled('status')) {
            $query->where('placement_status', $request->status);
        }

        $placements = $query->orderByDesc('created_at')->paginate($this->perPage($request));

        return $this->success([
            'items' => $placements->getCollection()->map(fn($placement) => $this->placementPayload($placement))->values(),
            'pagination' => [
                'current_page' => $placements->currentPage(),
                'last_page' => $placements->lastPage(),
                'per_page' => $placements->perPage(),
                'total' => $placements->total(),
            ],
        ]);
    }

    // ── PROTEGE Graduate endpoints ──────────────────────────────

    public function graduates(Request $request)
    {
        $query = Talent::whereNotNull('id_graduan');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('full_name', 'like', "%$s%")
                ->orWhere('id_graduan', 'like', "%$s%")
                ->orWhere('talent_code', 'like', "%$s%"));
        }
        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('id_pelaksana')) {
            $query->where('id_pelaksana', $request->id_pelaksana);
        }
        if ($request->filled('id_syarikat_penempatan')) {
            $query->where('id_syarikat_penempatan', $request->id_syarikat_penempatan);
        }

        $talents = $query->with(['syarikatPelaksana', 'syarikatPenempatan'])
            ->orderByDesc('updated_at')
            ->paginate($this->perPage($request));

        $isMiNDEF = auth()->user()?->hasRole('mindef_viewer');

        return $this->success([
            'items' => $talents->getCollection()->map(function (Talent $t) use ($isMiNDEF) {
                $item = [
                    'id' => $t->id,
                    'id_graduan' => $t->id_graduan,
                    'full_name' => $t->full_name,
                    'email' => $t->email,
                    'kategori' => $t->kategori,
                    'institusi' => $t->institusi,
                    'application_status' => $t->status,
                    'status' => $t->status_aktif ?? $t->status,
                    'pelaksana' => $t->syarikatPelaksana?->nama_syarikat,
                    'penempatan' => $t->syarikatPenempatan?->nama_syarikat,
                    'jawatan' => $t->jawatan,
                    'start_date' => $t->tarikh_mula?->toDateString(),
                    'end_date' => $t->tarikh_tamat?->toDateString(),
                ];
                if ($isMiNDEF) {
                    $item['full_name'] = DataMaskingService::maskName($t->full_name);
                }
                return $item;
            })->values(),
            'pagination' => [
                'current_page' => $talents->currentPage(),
                'last_page' => $talents->lastPage(),
                'per_page' => $talents->perPage(),
                'total' => $talents->total(),
            ],
        ]);
    }

    public function showGraduate(Talent $talent)
    {
        abort_unless($talent->id_graduan, 404);

        $talent->load(['syarikatPelaksana', 'syarikatPenempatan']);

        $isMiNDEF = auth()->user()?->hasRole('mindef_viewer');

        $data = [
            'id' => $talent->id,
            'id_graduan' => $talent->id_graduan,
            'full_name' => $talent->full_name,
            'ic_passport_no' => $talent->ic_passport_no,
            'phone' => $talent->phone,
            'email' => $talent->email,
            'kategori' => $talent->kategori,
            'kelayakan' => $talent->kelayakan,
            'institusi' => $talent->institusi,
            'status' => $talent->status_aktif ?? $talent->status,
            'jawatan' => $talent->jawatan,
            'pelaksana' => $talent->syarikatPelaksana?->nama_syarikat,
            'penempatan' => $talent->syarikatPenempatan?->nama_syarikat,
            'start_date' => $talent->tarikh_mula?->toDateString(),
            'end_date' => $talent->tarikh_tamat?->toDateString(),
            'elaun_bulanan' => $talent->elaun_bulanan,
            'status_penyerapan' => $talent->status_penyerapan_6bulan,
        ];

        if ($isMiNDEF) {
            $data['full_name'] = DataMaskingService::maskName($talent->full_name);
            $data['ic_passport_no'] = DataMaskingService::maskIcNumber($talent->ic_passport_no);
            $data['phone'] = DataMaskingService::maskPhone($talent->phone);
            $data['email'] = DataMaskingService::maskEmail($talent->email);
        }

        return $this->success(['graduate' => $data]);
    }

    public function issues(Request $request)
    {
        $query = IsuRisiko::with(['syarikatPelaksana', 'syarikatPenempatan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tahap_risiko')) {
            $query->where('tahap_risiko', $request->tahap_risiko);
        }
        if ($request->filled('id_pelaksana')) {
            $query->where('id_pelaksana', $request->id_pelaksana);
        }

        $records = $query->orderByDesc('tarikh_isu')->paginate($this->perPage($request));

        return $this->success([
            'items' => $records->getCollection()->map(fn($i) => [
                'id' => $i->id_isu,
                'date' => $i->tarikh_isu?->toDateString(),
                'category' => $i->kategori_isu,
                'severity' => $i->tahap_risiko,
                'status' => $i->status,
                'description' => \Illuminate\Support\Str::limit($i->butiran_isu, 120),
                'pic' => $i->pic,
                'pelaksana' => $i->syarikatPelaksana?->nama_syarikat,
                'penempatan' => $i->syarikatPenempatan?->nama_syarikat,
                'action_taken' => $i->tindakan_diambil,
            ])->values(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function profile()
    {
        $user = auth()->user();

        return $this->success([
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'language' => $user->language,
                'avatar' => $user->avatar ? \Storage::disk('public')->url($user->avatar) : null,
                'role' => $user->role?->role_name,
                'status' => $user->status,
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'language' => 'nullable|in:en,ms',
        ]);

        $user->update([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'language' => $validated['language'] ?? $user->language,
        ]);

        return $this->success([
            'user' => $this->userPayload($user->fresh()),
        ], 'Profile updated successfully.');
    }

    public function implementingCompanies(Request $request)
    {
        $query = SyarikatPelaksana::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_syarikat', 'like', "%{$search}%")
                    ->orWhere('id_pelaksana', 'like', "%{$search}%")
                    ->orWhere('pic_syarikat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_dana')) {
            $query->where('status_dana', $request->status_dana);
        }

        if ($request->filled('tahap_pematuhan')) {
            $query->where('tahap_pematuhan', $request->tahap_pematuhan);
        }

        $records = $query->orderBy('id_pelaksana')
            ->paginate($this->perPage($request));

        $currentYear = (string) date('Y');
        $pelaksanaIds = $records->getCollection()->pluck('id_pelaksana')->filter()->values();

        $companyIdsByCode = Company::whereIn('company_code', $pelaksanaIds)
            ->pluck('id', 'company_code');
        $companyIds = $companyIdsByCode->values()->filter()->unique()->values();

        $allocatedByCompany = BudgetAllocation::where('fiscal_year', $currentYear)
            ->whereIn('company_id', $companyIds)
            ->select('company_id', DB::raw('SUM(allocated_amount) as total_allocated'))
            ->groupBy('company_id')
            ->pluck('total_allocated', 'company_id');

        $budgetSpentByCompany = BudgetTransaction::where('status', 'approved')
            ->whereYear('transaction_date', (int) $currentYear)
            ->whereIn('company_id', $companyIds)
            ->select('company_id', DB::raw('SUM(amount) as total_spent'))
            ->groupBy('company_id')
            ->pluck('total_spent', 'company_id');

        $allowanceSpentByPelaksana = KewanganElaun::where('tahun', $currentYear)
            ->where('status_bayaran', 'Selesai')
            ->whereIn('id_pelaksana', $pelaksanaIds)
            ->select('id_pelaksana', DB::raw('SUM(elaun_prorate) as total_spent'))
            ->groupBy('id_pelaksana')
            ->pluck('total_spent', 'id_pelaksana');

        $items = $records->getCollection()->map(function (SyarikatPelaksana $item) use (
            $companyIdsByCode,
            $allocatedByCompany,
            $budgetSpentByCompany,
            $allowanceSpentByPelaksana
        ) {
            $companyId = $companyIdsByCode->get($item->id_pelaksana);
            $allocated = (float) ($allocatedByCompany->get($companyId) ?? $item->peruntukan_diluluskan ?? 0);
            $manualSpent = (float) ($budgetSpentByCompany->get($companyId) ?? 0);
            $allowanceSpent = (float) ($allowanceSpentByPelaksana->get($item->id_pelaksana) ?? 0);
            $used = $manualSpent + $allowanceSpent;
            $balance = $allocated - $used;
            $balancePct = $allocated > 0 ? ($balance / $allocated) * 100 : 0;
            $statusDana = $allocated <= 0
                ? 'Kritikal'
                : ($balancePct > 20 ? 'Mencukupi' : ($balancePct >= 10 ? 'Perlu Perhatian' : 'Kritikal'));

            return [
                'id' => $item->id_pelaksana,
                'company_name' => $item->nama_syarikat,
                'project' => $item->projek_kontrak,
                'pic_name' => $item->pic_syarikat,
                'pic_email' => $item->email_pic,
                'obligation_quota' => (int) $item->jumlah_kuota_obligasi,
                'approved_quota' => (int) $item->kuota_diluluskan,
                'used_quota' => (int) $item->kuota_digunakan,
                'allocated_budget' => $allocated,
                'used_budget' => $used,
                'balance_budget' => $balance,
                'usage_pct' => $allocated > 0 ? round(($used / $allocated) * 100, 1) : 0,
                'funding_status' => $statusDana,
                'compliance_level' => $item->tahap_pematuhan,
                'yellow_letter_status' => $item->status_surat_kuning,
                'blue_letter_status' => $item->status_surat_biru,
            ];
        })->values();

        return $this->success([
            'items' => $items,
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function showImplementingCompany(SyarikatPelaksana $syarikatPelaksana)
    {
        $syarikatPelaksana->loadCount('graduan');

        $suratRecords = StatusSurat::where('id_pelaksana', $syarikatPelaksana->id_pelaksana)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return $this->success([
            'company' => [
                'id' => $syarikatPelaksana->id_pelaksana,
                'company_name' => $syarikatPelaksana->nama_syarikat,
                'project' => $syarikatPelaksana->projek_kontrak,
                'pic_name' => $syarikatPelaksana->pic_syarikat,
                'pic_email' => $syarikatPelaksana->email_pic,
                'obligation_quota' => (int) $syarikatPelaksana->jumlah_kuota_obligasi,
                'approved_quota' => (int) $syarikatPelaksana->kuota_diluluskan,
                'used_quota' => (int) $syarikatPelaksana->kuota_digunakan,
                'allocated_budget' => (float) ($syarikatPelaksana->peruntukan_diluluskan ?? 0),
                'used_budget' => (float) ($syarikatPelaksana->peruntukan_diguna ?? 0),
                'balance_budget' => (float) ($syarikatPelaksana->baki_peruntukan ?? 0),
                'funding_status' => $syarikatPelaksana->status_dana,
                'compliance_level' => $syarikatPelaksana->tahap_pematuhan,
                'yellow_letter_status' => $syarikatPelaksana->status_surat_kuning,
                'blue_letter_status' => $syarikatPelaksana->status_surat_biru,
                'graduate_count' => $syarikatPelaksana->graduan_count,
            ],
            'letter_records' => $suratRecords->map(fn($record) => [
                'id' => $record->id_status_surat,
                'letter_type' => $record->jenis_surat,
                'status' => $record->status_surat,
                'start_date' => optional($record->tarikh_mula_proses)->toDateString(),
                'end_date' => optional($record->tarikh_siap)->toDateString(),
            ])->values(),
        ]);
    }

    public function placementCompanies(Request $request)
    {
        $query = SyarikatPenempatan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_syarikat', 'like', "%{$search}%")
                    ->orWhere('id_syarikat', 'like', "%{$search}%")
                    ->orWhere('sektor_industri', 'like', "%{$search}%")
                    ->orWhere('pic', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_pematuhan')) {
            $query->where('status_pematuhan', $request->status_pematuhan);
        }

        if ($request->filled('status_training')) {
            $query->where('status_training', $request->status_training);
        }

        if ($request->filled('sektor_industri')) {
            $query->where('sektor_industri', $request->sektor_industri);
        }

        $records = $query->orderBy('id_syarikat')
            ->paginate($this->perPage($request));

        return $this->success([
            'items' => $records->getCollection()->map(fn($item) => [
                'id' => $item->id_syarikat,
                'company_name' => $item->nama_syarikat,
                'company_type' => $item->jenis_syarikat,
                'sector' => $item->sektor_industri,
                'approved_quota' => (int) $item->kuota_dipersetujui,
                'graduate_count' => (int) $item->jumlah_graduan_ditempatkan,
                'pic_name' => $item->pic,
                'pic_phone' => $item->no_telefon_pic,
                'pic_email' => $item->email_pic,
                'monthly_report_status' => $item->laporan_bulanan,
                'compliance_status' => $item->status_pematuhan,
                'training_status' => $item->status_training,
                'training_compliance_pct' => (float) ($item->training_compliance_pct ?? 0),
                'notes' => $item->catatan,
            ])->values(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function showPlacementCompany(SyarikatPenempatan $syarikatPenempatan)
    {
        $syarikatPenempatan->loadCount('graduan');

        return $this->success([
            'company' => [
                'id' => $syarikatPenempatan->id_syarikat,
                'company_name' => $syarikatPenempatan->nama_syarikat,
                'company_type' => $syarikatPenempatan->jenis_syarikat,
                'sector' => $syarikatPenempatan->sektor_industri,
                'approved_quota' => (int) $syarikatPenempatan->kuota_dipersetujui,
                'graduate_count' => (int) $syarikatPenempatan->jumlah_graduan_ditempatkan,
                'pic_name' => $syarikatPenempatan->pic,
                'pic_phone' => $syarikatPenempatan->no_telefon_pic,
                'pic_email' => $syarikatPenempatan->email_pic,
                'monthly_report_status' => $syarikatPenempatan->laporan_bulanan,
                'compliance_status' => $syarikatPenempatan->status_pematuhan,
                'training_status' => $syarikatPenempatan->status_training,
                'training_compliance_pct' => (float) ($syarikatPenempatan->training_compliance_pct ?? 0),
                'notes' => $syarikatPenempatan->catatan,
                'graduate_total' => $syarikatPenempatan->graduan_count,
            ],
        ]);
    }

    public function managePlacements(Request $request)
    {
        $query = Talent::with(['syarikatPelaksana', 'syarikatPenempatan'])
            ->whereNotNull('id_graduan');

        if ($request->filled('assignment')) {
            if ($request->assignment === 'assigned') {
                $query->whereNotNull('id_syarikat_penempatan')
                    ->where('id_syarikat_penempatan', '!=', '');
            } elseif ($request->assignment === 'not_assigned') {
                $query->where(function ($q) {
                    $q->whereNull('id_syarikat_penempatan')
                        ->orWhere('id_syarikat_penempatan', '');
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

        $records = $query->orderBy('full_name')
            ->paginate($this->perPage($request));

        return $this->success([
            'items' => $records->getCollection()->map(fn($talent) => [
                'id' => $talent->id,
                'graduate_id' => $talent->id_graduan,
                'talent_code' => $talent->talent_code,
                'full_name' => $talent->full_name,
                'university' => $talent->university,
                'implementing_company' => $talent->syarikatPelaksana?->nama_syarikat,
                'placement_company' => $talent->syarikatPenempatan?->nama_syarikat,
                'status_aktif' => $talent->status_aktif,
                'assignment_status' => !empty($talent->id_syarikat_penempatan)
                    ? 'assigned'
                    : 'not_assigned',
            ])->values(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function showManagePlacement(Talent $talent)
    {
        abort_unless($talent->id_graduan, 404);

        $talent->load(['syarikatPelaksana', 'syarikatPenempatan', 'documents']);
        try {
            $talent->load('certifications');
        } catch (\Throwable $e) {
        }

        $logbooks = LogbookUpload::where('id_graduan', $talent->id_graduan)
            ->orderByDesc('tahun')
            ->orderByDesc('id')
            ->get();
        $dailyLogs = $talent->dailyLogs()->orderByDesc('log_date')->limit(20)->get();
        $transactions = KewanganElaun::where('id_graduan', $talent->id_graduan)
            ->orderByDesc('tahun')
            ->orderByDesc('id')
            ->get();
        $trainings = TrainingParticipant::with('trainingRecord')
            ->where('id_graduan', $talent->id_graduan)
            ->get();
        $suratRecords = StatusSurat::where('id_graduan', $talent->id_graduan)
            ->orderByDesc('tarikh_mula_proses')
            ->get();
        $placements = $talent->placements()->with('feedback')->get();
        $feedbacks = $placements->flatMap(fn($p) => $p->feedback);

        return $this->success([
            'talent' => [
                'id' => $talent->id,
                'graduate_id' => $talent->id_graduan,
                'full_name' => $talent->full_name,
                'email' => $talent->email,
                'phone' => $talent->phone,
                'university' => $talent->university,
                'programme' => $talent->programme,
                'status_aktif' => $talent->status_aktif,
                'status_penyerapan_6bulan' => $talent->status_penyerapan_6bulan,
                'jawatan' => $talent->jawatan,
                'tarikh_mula' => optional($talent->tarikh_mula)->toDateString(),
                'tarikh_tamat' => optional($talent->tarikh_tamat)->toDateString(),
                'department' => $talent->department,
                'supervisor_name' => $talent->supervisor_name,
                'supervisor_email' => $talent->supervisor_email,
                'monthly_stipend' => (float) ($talent->monthly_stipend ?? 0),
                'programme_type' => $talent->programme_type,
                'implementing_company' => $talent->syarikatPelaksana ? [
                    'id' => $talent->syarikatPelaksana->id_pelaksana,
                    'name' => $talent->syarikatPelaksana->nama_syarikat,
                ] : null,
                'placement_company' => $talent->syarikatPenempatan ? [
                    'id' => $talent->syarikatPenempatan->id_syarikat,
                    'name' => $talent->syarikatPenempatan->nama_syarikat,
                ] : null,
            ],
            'logbooks' => $logbooks->map(fn($record) => [
                'id' => $record->id,
                'month' => $record->bulan,
                'year' => $record->tahun,
                'status' => $record->status_upload,
                'review_status' => $record->status_semakan,
                'mentor_comment' => $record->komen_mentor,
                'description' => $record->deskripsi_tugas,
            ])->values(),
            'daily_logs' => $dailyLogs->map(fn($record) => [
                'id' => $record->id,
                'date' => optional($record->log_date)->toDateString(),
                'tasks' => $record->tasks,
                'notes' => $record->notes,
                'status' => $record->status,
            ])->values(),
            'transactions' => $transactions->map(fn($record) => [
                'id' => $record->id,
                'month' => $record->bulan,
                'year' => $record->tahun,
                'amount' => (float) ($record->elaun_prorate ?? 0),
                'payment_status' => $record->status_bayaran,
            ])->values(),
            'trainings' => $trainings->map(fn($record) => [
                'id' => $record->id,
                'title' => $record->trainingRecord?->tajuk_latihan,
                'date' => optional($record->trainingRecord?->tarikh_latihan)->toDateString(),
                'session' => $record->trainingRecord?->sesi,
                'status' => $record->trainingRecord?->status,
            ])->values(),
            'letters' => $suratRecords->map(fn($record) => [
                'id' => $record->id_status_surat,
                'type' => $record->jenis_surat,
                'status' => $record->status_surat,
                'start_date' => optional($record->tarikh_mula_proses)->toDateString(),
                'end_date' => optional($record->tarikh_siap)->toDateString(),
            ])->values(),
            'feedbacks' => $feedbacks->map(fn($record) => [
                'id' => $record->id,
                'feedback_from' => $record->feedback_from,
                'comments' => $record->comments,
                'submitted_at' => optional($record->submitted_at)->toIso8601String(),
            ])->values(),
            'placements' => $placements->map(fn($placement) => [
                'id' => $placement->id,
                'status' => $placement->placement_status,
                'start_date' => optional($placement->start_date)->toDateString(),
                'end_date' => optional($placement->end_date)->toDateString(),
            ])->values(),
            'documents' => $talent->documents->map(fn($document) => [
                'id' => $document->id,
                'document_type' => $document->document_type,
                'file_name' => $document->file_name,
            ])->values(),
            'certifications' => collect($talent->certifications ?? [])->map(fn($cert) => [
                'id' => $cert->id,
                'name' => $cert->certificate_name ?? $cert->name ?? '-',
            ])->values(),
            'implementing_companies' => SyarikatPelaksana::orderBy('nama_syarikat')
                ->get(['id_pelaksana', 'nama_syarikat'])
                ->map(fn($item) => [
                    'id' => $item->id_pelaksana,
                    'name' => $item->nama_syarikat,
                ])->values(),
            'placement_companies' => SyarikatPenempatan::orderBy('nama_syarikat')
                ->get(['id_syarikat', 'nama_syarikat'])
                ->map(fn($item) => [
                    'id' => $item->id_syarikat,
                    'name' => $item->nama_syarikat,
                ])->values(),
        ]);
    }

    public function assignManagePlacement(Request $request, Talent $talent)
    {
        abort_unless($talent->id_graduan, 404);

        $validated = $request->validate([
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
        $updateData = [
            'id_pelaksana' => $validated['id_pelaksana'] ?? null,
            'id_syarikat_penempatan' => $validated['id_syarikat_penempatan'],
            'jawatan' => $validated['jawatan'] ?? null,
            'tarikh_mula' => $validated['tarikh_mula'] ?? null,
            'tarikh_tamat' => $validated['tarikh_tamat'] ?? null,
            'department' => $validated['department'] ?? null,
            'supervisor_name' => $validated['supervisor_name'] ?? null,
            'supervisor_email' => $validated['supervisor_email'] ?? null,
            'monthly_stipend' => $validated['monthly_stipend'] ?? null,
            'status_aktif' => $talent->status_aktif ?: 'Aktif',
        ];

        if (!empty($validated['status_penyerapan_6bulan'])) {
            $updateData['status_penyerapan_6bulan'] = $validated['status_penyerapan_6bulan'];
        }

        $talent->update($updateData);
        AuditLog::log('talents', 'assign_placement', $talent->id, $old, $talent->fresh()->toArray());

        return $this->success([
            'talent' => [
                'id' => $talent->id,
                'status_aktif' => $talent->fresh()->status_aktif,
            ],
        ], 'Placement assigned successfully.');
    }

    public function completeManagePlacement(Request $request, Talent $talent)
    {
        abort_unless($talent->id_graduan, 404);

        $validated = $request->validate([
            'completion_date' => 'required|date',
            'status_penyerapan_6bulan' => 'required|in:Diserap,Tidak Diserap,Dalam Proses,Belum Layak',
            'completion_remarks' => 'nullable|string|max:1000',
        ]);

        $old = $talent->toArray();
        $talent->update([
            'status_aktif' => 'Tamat',
            'tarikh_tamat' => $validated['completion_date'],
            'status_penyerapan_6bulan' => $validated['status_penyerapan_6bulan'],
        ]);

        $talent->placements()->whereIn('placement_status', ['active', 'confirmed'])->update([
            'placement_status' => 'completed',
            'end_date' => $validated['completion_date'],
            'remarks' => $validated['completion_remarks'] ?? null,
        ]);

        AuditLog::log('talents', 'complete_placement', $talent->id, $old, $talent->fresh()->toArray());

        return $this->success([
            'talent' => [
                'id' => $talent->id,
                'status_aktif' => $talent->fresh()->status_aktif,
            ],
        ], 'Placement completed successfully.');
    }

    public function terminateManagePlacement(Request $request, Talent $talent)
    {
        abort_unless($talent->id_graduan, 404);

        $validated = $request->validate([
            'termination_date' => 'required|date',
            'termination_reason' => 'required|string|max:500',
        ]);

        $old = $talent->toArray();
        $talent->update([
            'status_aktif' => 'Berhenti Awal',
            'tarikh_tamat' => $validated['termination_date'],
        ]);

        $talent->placements()->whereIn('placement_status', ['active', 'confirmed'])->update([
            'placement_status' => 'terminated',
            'end_date' => $validated['termination_date'],
            'remarks' => $validated['termination_reason'],
        ]);

        AuditLog::log('talents', 'early_termination', $talent->id, $old, $talent->fresh()->toArray());

        return $this->success([
            'talent' => [
                'id' => $talent->id,
                'status_aktif' => $talent->fresh()->status_aktif,
            ],
        ], 'Placement terminated successfully.');
    }

    public function storeManagePlacementFeedback(Request $request, Talent $talent)
    {
        abort_unless($talent->id_graduan, 404);

        $validated = $request->validate([
            'placement_id' => 'required|exists:placements,id',
            'feedback_from' => 'required|in:company,yltat',
            'score_technical' => 'required|integer|min:1|max:5',
            'score_communication' => 'required|integer|min:1|max:5',
            'score_discipline' => 'required|integer|min:1|max:5',
            'score_problem_solving' => 'required|integer|min:1|max:5',
            'score_professionalism' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
        ]);

        $feedback = InternshipFeedback::create([
            'placement_id' => $validated['placement_id'],
            'feedback_from' => $validated['feedback_from'],
            'score_technical' => $validated['score_technical'],
            'score_communication' => $validated['score_communication'],
            'score_discipline' => $validated['score_discipline'],
            'score_problem_solving' => $validated['score_problem_solving'],
            'score_professionalism' => $validated['score_professionalism'],
            'comments' => $validated['comments'] ?? null,
            'submitted_at' => now(),
        ]);

        return $this->success([
            'feedback' => [
                'id' => $feedback->id,
                'feedback_from' => $feedback->feedback_from,
                'comments' => $feedback->comments,
            ],
        ], 'Feedback submitted successfully.');
    }

    public function attendance(Request $request)
    {
        $query = KehadiranPrestasi::with(['graduan', 'syarikatPelaksana', 'syarikatPenempatan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_graduan', 'like', "%{$search}%")
                    ->orWhereHas('graduan', fn ($g) => $g->where('full_name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        $records = $query->orderByDesc('tahun')->orderByDesc('bulan')->paginate($this->perPage($request));

        return $this->success([
            'items' => $records->getCollection()->map(fn ($record) => [
                'id' => $record->id,
                'graduate_id' => $record->id_graduan,
                'graduate_name' => $record->graduan?->full_name ?? $record->nama_graduan ?? '-',
                'month' => $record->bulan,
                'year' => $record->tahun,
                'attendance_pct' => (float) $record->kehadiran_pct,
                'days_present' => (int) $record->hari_hadir,
                'working_days' => (int) $record->hari_bekerja,
                'performance_score' => (int) $record->skor_prestasi,
                'mentor_comment' => $record->komen_mentor,
                'logbook_status' => $record->status_logbook,
                'implementing_company' => $record->syarikatPelaksana?->nama_syarikat,
                'placement_company' => $record->syarikatPenempatan?->nama_syarikat,
            ])->values(),
        ]);
    }

    public function dailyLogs(Request $request)
    {
        $query = DailyLog::with(['talent.syarikatPenempatan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('activities', 'like', "%{$search}%")
                    ->orWhereHas('talent', fn ($g) => $g->where('full_name', 'like', "%{$search}%")
                        ->orWhere('id_graduan', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderByDesc('log_date')->paginate($this->perPage($request));

        return $this->success([
            'items' => $records->getCollection()->map(fn ($record) => [
                'id' => $record->id,
                'date' => optional($record->log_date)->toDateString(),
                'graduate_id' => $record->talent?->id_graduan,
                'graduate_name' => $record->talent?->full_name ?? '-',
                'placement_company' => $record->talent?->syarikatPenempatan?->nama_syarikat,
                'activities' => $record->activities,
                'challenges' => $record->challenges,
                'learnings' => $record->learnings,
                'mood' => $record->mood,
                'status' => $record->status,
                'admin_remarks' => $record->admin_remarks,
            ])->values(),
        ]);
    }

    public function logbooks(Request $request)
    {
        $query = LogbookUpload::with(['graduan', 'syarikatPenempatan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_graduan', 'like', "%{$search}%")
                    ->orWhere('nama_graduan', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status_semakan')) {
            $query->where('status_semakan', $request->status_semakan);
        }

        $records = $query->orderByDesc('tahun')->orderByDesc('bulan')->paginate($this->perPage($request));

        return $this->success([
            'items' => $records->getCollection()->map(fn ($record) => [
                'id' => $record->id,
                'graduate_id' => $record->id_graduan,
                'graduate_name' => $record->nama_graduan,
                'company_name' => $record->nama_syarikat ?: $record->syarikatPenempatan?->nama_syarikat,
                'month' => $record->bulan,
                'year' => $record->tahun,
                'submission_status' => $record->status_logbook,
                'review_status' => $record->status_semakan,
                'mentor_name' => $record->nama_mentor,
                'mentor_comment' => $record->komen_mentor,
                'upload_date' => optional($record->tarikh_upload)->toDateString(),
                'file_name' => $record->file_name,
                'file_link' => $record->link_file_logbook,
            ])->values(),
        ]);
    }

    public function training(Request $request)
    {
        $query = TrainingRecord::with('participants');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_training', 'like', "%{$search}%")
                    ->orWhere('tajuk_training', 'like', "%{$search}%")
                    ->orWhere('nama_syarikat', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('sesi')) {
            $query->where('sesi', $request->sesi);
        }

        $records = $query->orderByDesc('tarikh_training')->paginate($this->perPage($request));

        return $this->success([
            'items' => $records->getCollection()->map(fn ($record) => [
                'id' => $record->id,
                'training_id' => $record->id_training,
                'company_name' => $record->nama_syarikat,
                'title' => $record->tajuk_training,
                'session' => $record->sesi,
                'date' => optional($record->tarikh_training)->toDateString(),
                'attendance_pct' => (float) $record->kadar_kehadiran_pct,
                'improvement_pct' => (float) ($record->improvement_pct ?? 0),
                'status' => $record->status,
                'participant_count' => $record->participants->count(),
                'location' => $record->lokasi,
                'trainer' => $record->trainer_name,
                'budget_allocated' => (float) ($record->budget_allocated ?? 0),
                'budget_spent' => (float) ($record->budget_spent ?? 0),
            ])->values(),
        ]);
    }

    public function letterStatus(Request $request)
    {
        $query = StatusSurat::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_graduan', 'like', "%{$search}%")
                    ->orWhere('id_graduan', 'like', "%{$search}%")
                    ->orWhere('jenis_surat', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status_surat')) {
            $query->where('status_surat', $request->status_surat);
        }

        $records = $query->orderByDesc('tarikh_mula_proses')->paginate($this->perPage($request));

        return $this->success([
            'items' => $records->getCollection()->map(fn ($record) => [
                'id' => $record->id_status_surat,
                'type' => $record->jenis_surat,
                'graduate_id' => $record->id_graduan,
                'graduate_name' => $record->nama_graduan,
                'status' => $record->status_surat,
                'start_date' => optional($record->tarikh_mula_proses)->toDateString(),
                'completed_date' => optional($record->tarikh_siap)->toDateString(),
                'pic' => $record->pic_responsible,
                'issue' => $record->isu_halangan,
                'notes' => $record->catatan,
                'sla_status' => $record->sla_status,
            ])->values(),
        ]);
    }

    public function finance(Request $request)
    {
        $query = KewanganElaun::with(['graduan.syarikatPelaksana']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_graduan', 'like', "%{$search}%")
                    ->orWhereHas('graduan', fn ($g) => $g->where('full_name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('status_bayaran')) {
            $query->where('status_bayaran', $request->status_bayaran);
        }

        $records = $query->orderByDesc('tahun')->orderByDesc('bulan')->paginate($this->perPage($request));

        return $this->success([
            'items' => $records->getCollection()->map(fn ($record) => [
                'id' => $record->id,
                'graduate_id' => $record->id_graduan,
                'graduate_name' => $record->graduan?->full_name ?? '-',
                'implementing_company' => $record->graduan?->syarikatPelaksana?->nama_syarikat,
                'month' => $record->bulan,
                'year' => $record->tahun,
                'amount' => (float) ($record->elaun_prorate ?? 0),
                'full_amount' => (float) ($record->elaun_penuh ?? 0),
                'payment_status' => $record->status_bayaran,
                'payment_date' => optional($record->tarikh_bayar)->toDateString(),
                'expected_payment_date' => optional($record->tarikh_jangka_bayar)->toDateString(),
                'late_days' => (int) ($record->hari_lewat ?? 0),
                'notes' => $record->catatan,
            ])->values(),
        ]);
    }

    public function budget()
    {
        $year = (int) date('Y');
        $allocations = BudgetAllocation::with(['company', 'batch'])
            ->where('fiscal_year', $year)
            ->orderByDesc('allocated_amount')
            ->get();
        $transactions = BudgetTransaction::with(['company', 'talent'])
            ->whereYear('transaction_date', $year)
            ->orderByDesc('transaction_date')
            ->get();

        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthMap = [
            'Januari' => 1, 'Februari' => 2, 'Mac' => 3, 'April' => 4,
            'Mei' => 5, 'Jun' => 6, 'Julai' => 7, 'Ogos' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Disember' => 12,
        ];

        $budgetTrend = BudgetTransaction::select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'approved')
            ->whereYear('transaction_date', $year)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $kewanganTrend = KewanganElaun::where('tahun', $year)
            ->where('status_bayaran', 'Selesai')
            ->get()
            ->groupBy('bulan')
            ->mapWithKeys(fn ($items, $bulan) => [
                $monthMap[$bulan] ?? 0 => (float) $items->sum('elaun_prorate'),
            ]);

        $monthlyTrend = collect(range(1, 12))->map(function ($month) use ($monthLabels, $budgetTrend, $kewanganTrend) {
            $budgetTotal = isset($budgetTrend[$month]) ? (float) $budgetTrend[$month]->total : 0;
            $kewanganTotal = isset($kewanganTrend[$month]) ? (float) $kewanganTrend[$month] : 0;

            return [
                'label' => $monthLabels[$month - 1],
                'value' => $budgetTotal + $kewanganTotal,
            ];
        })->values();

        $budgetByCompany = BudgetTransaction::select('company_id', DB::raw('SUM(amount) as total'))
            ->where('status', 'approved')
            ->whereYear('transaction_date', $year)
            ->whereNotNull('company_id')
            ->groupBy('company_id')
            ->get();

        $kewanganByPelaksana = KewanganElaun::where('tahun', $year)
            ->where('status_bayaran', 'Selesai')
            ->whereNotNull('id_pelaksana')
            ->select('id_pelaksana', DB::raw('SUM(elaun_prorate) as total'))
            ->groupBy('id_pelaksana')
            ->get();

        $companyTotals = collect();
        foreach ($budgetByCompany as $row) {
            $company = Company::find($row->company_id);
            $name = $company?->company_name ?? 'Unknown';
            $companyTotals[$name] = ($companyTotals[$name] ?? 0) + (float) $row->total;
        }
        foreach ($kewanganByPelaksana as $row) {
            $pelaksana = SyarikatPelaksana::find($row->id_pelaksana);
            $name = $pelaksana?->nama_syarikat ?? $row->id_pelaksana;
            $companyTotals[$name] = ($companyTotals[$name] ?? 0) + (float) $row->total;
        }

        $companyBreakdown = $companyTotals
            ->sortByDesc(fn ($value) => $value)
            ->map(fn ($value, $label) => [
                'label' => $label,
                'value' => (float) $value,
            ])
            ->values();

        $recentBudgetTransactions = $transactions->take(8)->map(fn ($item) => [
            'id' => 'budget-' . $item->id,
            'title' => $item->company?->company_name ?? $item->talent?->full_name ?? 'General',
            'subtitle' => trim(($item->transaction_date?->toDateString() ?? '-') . ' • ' . ucfirst((string) $item->category)),
            'amount' => (float) $item->amount,
            'source' => 'budget',
            'status' => $item->status,
            'date' => optional($item->transaction_date)->toDateString(),
            'description' => $item->description,
        ]);

        $recentAllowanceTransactions = KewanganElaun::with('graduan')
            ->where('status_bayaran', 'Selesai')
            ->orderByDesc('tarikh_bayar')
            ->limit(8)
            ->get()
            ->map(fn ($item) => [
                'id' => 'allowance-' . $item->id,
                'title' => $item->graduan?->full_name ?? 'General',
                'subtitle' => trim(($item->tarikh_bayar?->toDateString() ?? '-') . ' • Monthly Allowance'),
                'amount' => (float) ($item->elaun_prorate ?? 0),
                'source' => 'allowance',
                'status' => $item->status_bayaran,
                'date' => optional($item->tarikh_bayar)->toDateString(),
                'description' => ($item->bulan ?? '-') . ' ' . ($item->tahun ?? ''),
            ]);

        $recentTransactions = $recentBudgetTransactions
            ->concat($recentAllowanceTransactions)
            ->sortByDesc('amount')
            ->take(8)
            ->values();

        return $this->success([
            'summary' => [
                'year' => $year,
                'allocated_total' => (float) $allocations->sum('allocated_amount'),
                'transaction_total' => (float) $transactions->sum('amount'),
                'approved_total' => (float) $transactions->where('status', 'approved')->sum('amount'),
            ],
            'monthly_trend' => $monthlyTrend,
            'company_breakdown' => $companyBreakdown,
            'allocations' => $allocations->map(fn ($item) => [
                'id' => $item->id,
                'company_name' => $item->company?->company_name ?? '-',
                'batch' => $item->batch?->batch_name ?? '-',
                'fiscal_year' => $item->fiscal_year,
                'allocated_amount' => (float) $item->allocated_amount,
                'remarks' => $item->remarks,
            ])->values(),
            'transactions' => $recentTransactions,
        ]);
    }

    public function budgetTransactions(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $category = trim((string) $request->get('category', ''));

        $budgetRows = BudgetTransaction::with(['talent', 'company'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('reference_no', 'like', "%{$search}%");
                });
            })
            ->when($category !== '' && $category !== 'allowance', fn ($query) => $query->where('category', $category))
            ->when($category === 'allowance', fn ($query) => $query->whereRaw('1 = 0'))
            ->orderByDesc('transaction_date')
            ->get()
            ->map(fn ($item) => [
                'id' => 'budget-' . $item->id,
                'date' => optional($item->transaction_date)->toDateString(),
                'title' => $item->company?->company_name ?? $item->talent?->full_name ?? 'General',
                'subtitle' => ucfirst((string) $item->category),
                'description' => $item->description,
                'amount' => (float) $item->amount,
                'status' => $item->status,
                'source' => 'budget',
                'reference_no' => $item->reference_no,
            ]);

        $allowanceRows = KewanganElaun::with('graduan')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id_graduan', 'like', "%{$search}%")
                        ->orWhereHas('graduan', fn ($q2) => $q2->where('full_name', 'like', "%{$search}%"));
                });
            })
            ->when($category !== '' && $category !== 'allowance', fn ($query) => $query->whereRaw('1 = 0'))
            ->orderByDesc('tarikh_bayar')
            ->get()
            ->map(fn ($item) => [
                'id' => 'allowance-' . $item->id,
                'date' => optional($item->tarikh_bayar)->toDateString(),
                'title' => $item->graduan?->full_name ?? $item->id_graduan ?? 'General',
                'subtitle' => trim(($item->bulan ?? '-') . ' ' . ($item->tahun ?? '')),
                'description' => 'Monthly Allowance',
                'amount' => (float) ($item->elaun_prorate ?? 0),
                'status' => $item->status_bayaran,
                'source' => 'allowance',
                'reference_no' => null,
            ]);

        return $this->success([
            'items' => $budgetRows->concat($allowanceRows)->sortByDesc('date')->values(),
        ]);
    }

    public function budgetAllocations(Request $request)
    {
        $year = trim((string) $request->get('fiscal_year', date('Y')));

        return $this->success([
            'items' => BudgetAllocation::with(['company', 'batch'])
                ->where('fiscal_year', $year)
                ->orderByDesc('allocated_amount')
                ->get()
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'company_name' => $item->company?->company_name ?? '-',
                    'batch' => $item->batch?->batch_name ?? '-',
                    'fiscal_year' => $item->fiscal_year,
                    'allocated_amount' => (float) $item->allocated_amount,
                    'remarks' => $item->remarks,
                ])
                ->values(),
        ]);
    }

    public function storeBudgetAllocation(Request $request)
    {
        $validated = $request->validate([
            'fiscal_year' => 'required|string|max:10',
            'id_pelaksana' => 'nullable|exists:syarikat_pelaksana,id_pelaksana',
            'allocated_amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $companyId = null;
        if (!empty($validated['id_pelaksana'])) {
            $company = Company::where('company_code', $validated['id_pelaksana'])->first();
            $companyId = $company?->id;
        }

        $allocation = BudgetAllocation::create([
            'fiscal_year' => $validated['fiscal_year'],
            'company_id' => $companyId,
            'allocated_amount' => $validated['allocated_amount'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        AuditLog::log('budget', 'create_allocation', $allocation->id, null, $allocation->toArray());

        return $this->success([
            'allocation' => [
                'id' => $allocation->id,
                'fiscal_year' => $allocation->fiscal_year,
                'allocated_amount' => (float) $allocation->allocated_amount,
                'remarks' => $allocation->remarks,
            ],
        ], 'Allocation created successfully.');
    }

    public function destroyBudgetAllocation(BudgetAllocation $allocation)
    {
        $old = $allocation->toArray();
        $allocation->delete();

        AuditLog::log('budget', 'delete_allocation', $allocation->id, $old, null);

        return $this->success([], 'Allocation deleted successfully.');
    }

    public function kpi()
    {
        $records = KpiDashboard::orderByDesc('tahun')->orderByDesc('bulan')->limit(12)->get();

        return $this->success([
            'items' => $records->map(fn ($item) => [
                'id' => $item->id,
                'month' => $item->bulan,
                'year' => $item->tahun,
                'active_graduates' => $item->total_graduan_aktif,
                'retention_rate_pct' => (float) $item->retention_rate_pct,
                'avg_attendance_pct' => (float) $item->avg_kehadiran_pct,
                'avg_performance_score' => (float) $item->avg_prestasi_score,
                'logbook_submitted_pct' => (float) $item->logbook_submitted_pct,
                'budget_utilization_pct' => (float) $item->budget_utilization_pct,
                'training_compliance_pct' => (float) $item->training_compliance_rate_pct,
                'avg_skill_improvement_pct' => (float) $item->avg_skill_improvement_pct,
            ])->values(),
        ]);
    }

    public function reports()
    {
        return $this->success([
            'summary' => [
                'talent_records' => Talent::whereNotNull('id_graduan')->count(),
                'placement_records' => Placement::count(),
                'company_records' => SyarikatPenempatan::count(),
                'training_records' => TrainingRecord::count(),
            ],
            'exports' => [
                [
                    'id' => 'talent',
                    'label' => 'Talent Report',
                    'status' => 'ready',
                    'pdf_path' => '/admin/reports/talent/pdf',
                ],
                [
                    'id' => 'company',
                    'label' => 'Company Report',
                    'status' => 'ready',
                    'pdf_path' => '/admin/reports/company/pdf',
                ],
                [
                    'id' => 'budget',
                    'label' => 'Budget Report',
                    'status' => 'ready',
                    'pdf_path' => '/admin/reports/budget/pdf',
                ],
                [
                    'id' => 'placement',
                    'label' => 'Placement Report',
                    'status' => 'ready',
                    'pdf_path' => '/admin/reports/placement/pdf',
                ],
                [
                    'id' => 'training',
                    'label' => 'Training Report',
                    'status' => 'ready',
                    'pdf_path' => '/admin/reports/training/pdf',
                ],
                [
                    'id' => 'executive',
                    'label' => 'Executive Export',
                    'status' => 'ready',
                    'pdf_path' => '/admin/reports/executive/pdf',
                ],
            ],
        ]);
    }

    public function reportPdf(string $type)
    {
        $allowed = ['talent', 'company', 'budget', 'placement', 'training', 'executive'];
        if (!in_array($type, $allowed, true)) {
            return $this->error('Report type not found.', 404);
        }

        [$title, $lines] = match ($type) {
            'talent' => $this->buildTalentReportPdfData(),
            'company' => $this->buildCompanyReportPdfData(),
            'budget' => $this->buildBudgetReportPdfData(),
            'placement' => $this->buildPlacementReportPdfData(),
            'training' => $this->buildTrainingReportPdfData(),
            'executive' => $this->buildExecutiveReportPdfData(),
        };

        $pdf = $this->buildSimplePdf($title, $lines);
        $filename = strtolower(str_replace(' ', '-', $title)) . '.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    private function buildTalentReportPdfData(): array
    {
        $total = Talent::whereNotNull('id_graduan')->count();
        $active = Talent::where('status_aktif', 'Aktif')->count();
        $universities = Talent::select('university')
            ->whereNotNull('university')
            ->distinct()
            ->count('university');
        $latest = Talent::whereNotNull('id_graduan')
            ->latest('id')
            ->limit(8)
            ->get(['id_graduan', 'full_name', 'university', 'status_aktif']);

        $lines = [
            'Total talent records: ' . $total,
            'Active graduates: ' . $active,
            'Universities represented: ' . $universities,
            '',
            'Latest records:',
        ];

        foreach ($latest as $item) {
            $lines[] = trim(($item->id_graduan ?? '-') . ' | ' . ($item->full_name ?? '-') . ' | ' . ($item->university ?? '-') . ' | ' . ($item->status_aktif ?? '-'));
        }

        return ['Talent Report', $lines];
    }

    private function buildCompanyReportPdfData(): array
    {
        $placementCompanies = SyarikatPenempatan::count();
        $implementingCompanies = SyarikatPelaksana::count();
        $latestPlacementCompanies = SyarikatPenempatan::latest('id_syarikat')
            ->limit(8)
            ->get(['id_syarikat', 'nama_syarikat', 'sektor_industri', 'status_pematuhan']);

        $lines = [
            'Placement companies: ' . $placementCompanies,
            'Implementing companies: ' . $implementingCompanies,
            '',
            'Latest placement companies:',
        ];

        foreach ($latestPlacementCompanies as $item) {
            $lines[] = trim(($item->id_syarikat ?? '-') . ' | ' . ($item->nama_syarikat ?? '-') . ' | ' . ($item->sektor_industri ?? '-') . ' | ' . ($item->status_pematuhan ?? '-'));
        }

        return ['Company Report', $lines];
    }

    private function buildBudgetReportPdfData(): array
    {
        $allocated = (float) BudgetAllocation::sum('allocated_amount');
        $spent = (float) BudgetTransaction::sum('amount');
        $approved = (float) BudgetTransaction::where('status', 'approved')->sum('amount');
        $recent = BudgetTransaction::with(['company', 'talent'])
            ->latest('transaction_date')
            ->limit(10)
            ->get();

        $lines = [
            'Allocated budget: RM ' . number_format($allocated, 2),
            'Total spent: RM ' . number_format($spent, 2),
            'Approved amount: RM ' . number_format($approved, 2),
            '',
            'Recent transactions:',
        ];

        foreach ($recent as $item) {
            $title = $item->company?->company_name ?? $item->talent?->full_name ?? 'General';
            $lines[] = trim(($item->transaction_date?->toDateString() ?? '-') . ' | ' . $title . ' | RM ' . number_format((float) $item->amount, 2) . ' | ' . ($item->status ?? '-'));
        }

        return ['Budget Report', $lines];
    }

    private function buildPlacementReportPdfData(): array
    {
        $total = Placement::count();
        $active = Placement::whereIn('placement_status', ['active', 'confirmed'])->count();
        $completed = Placement::where('placement_status', 'completed')->count();
        $latest = Placement::with(['talent', 'company'])
            ->latest('start_date')
            ->limit(10)
            ->get();

        $lines = [
            'Total placements: ' . $total,
            'Active placements: ' . $active,
            'Completed placements: ' . $completed,
            '',
            'Latest placements:',
        ];

        foreach ($latest as $item) {
            $lines[] = trim(($item->talent?->full_name ?? '-') . ' | ' . ($item->company?->company_name ?? '-') . ' | ' . ($item->placement_status ?? '-') . ' | ' . ($item->start_date?->toDateString() ?? '-'));
        }

        return ['Placement Report', $lines];
    }

    private function buildTrainingReportPdfData(): array
    {
        $total = TrainingRecord::count();
        $completed = TrainingRecord::where('status', 'Selesai')->count();
        $avgSatisfaction = (float) TrainingRecord::where('status', 'Selesai')->avg('skor_kepuasan');
        $latest = TrainingRecord::latest('tarikh_training')
            ->limit(10)
            ->get(['id_training', 'tajuk_training', 'sesi', 'tarikh_training', 'status']);

        $lines = [
            'Total training records: ' . $total,
            'Completed sessions: ' . $completed,
            'Average satisfaction: ' . number_format($avgSatisfaction, 2),
            '',
            'Latest training records:',
        ];

        foreach ($latest as $item) {
            $lines[] = trim(($item->id_training ?? '-') . ' | ' . ($item->tajuk_training ?? '-') . ' | ' . ($item->sesi ?? '-') . ' | ' . ($item->tarikh_training?->toDateString() ?? '-') . ' | ' . ($item->status ?? '-'));
        }

        return ['Training Report', $lines];
    }

    private function buildExecutiveReportPdfData(): array
    {
        $totalTalent = Talent::count();
        $activeTalent = Talent::where('status_aktif', 'Aktif')->count();
        $openIssues = IsuRisiko::whereIn('status', ['Baru', 'Dalam Tindakan'])->count();
        $latestKpi = KpiDashboard::latest('tahun')->latest('id')->first();

        $lines = [
            'Total talent: ' . $totalTalent,
            'Active talent: ' . $activeTalent,
            'Open issues: ' . $openIssues,
            'Latest KPI year: ' . ($latestKpi?->tahun ?? '-'),
            'Latest retention rate: ' . number_format((float) ($latestKpi?->retention_rate_pct ?? 0), 1) . '%',
            'Latest attendance rate: ' . number_format((float) ($latestKpi?->avg_kehadiran_pct ?? 0), 1) . '%',
        ];

        return ['Executive Export', $lines];
    }

    private function buildSimplePdf(string $title, array $lines): string
    {
        $contentLines = array_merge([$title, ''], $lines);
        $y = 800;
        $commands = ['BT', '/F1 18 Tf', '40 820 Td', '(' . $this->escapePdfText($title) . ') Tj'];
        $commands[] = '/F1 11 Tf';

        foreach ($contentLines as $index => $line) {
            if ($index === 0) {
                continue;
            }
            $y -= 18;
            if ($y < 40) {
                break;
            }
            $commands[] = '1 0 0 1 40 ' . $y . ' Tm';
            $commands[] = '(' . $this->escapePdfText((string) $line) . ') Tj';
        }

        $commands[] = 'ET';
        $stream = implode("\n", $commands);
        $length = strlen($stream);

        $objects = [];
        $objects[] = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
        $objects[] = '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj';
        $objects[] = '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj';
        $objects[] = '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj';
        $objects[] = "5 0 obj << /Length {$length} >> stream\n{$stream}\nendstream endobj";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object . "\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= 'xref' . "\n";
        $pdf .= '0 ' . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }
        $pdf .= 'trailer << /Size ' . (count($objects) + 1) . ' /Root 1 0 R >>' . "\n";
        $pdf .= 'startxref' . "\n";
        $pdf .= $xrefOffset . "\n";
        $pdf .= "%%EOF";

        return $pdf;
    }

    private function escapePdfText(string $value): string
    {
        $value = preg_replace('/[^\x20-\x7E]/', '?', $value) ?? '';
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace('(', '\\(', $value);
        $value = str_replace(')', '\\)', $value);
        return $value;
    }

    public function feedback(Request $request)
    {
        $query = InternshipFeedback::with(['placement.talent', 'placement.company']);

        if ($request->filled('feedback_from')) {
            $query->where('feedback_from', $request->feedback_from);
        }

        $records = $query->orderByDesc('submitted_at')->paginate($this->perPage($request));

        return $this->success([
            'items' => $records->getCollection()->map(fn ($item) => [
                'id' => $item->id,
                'feedback_from' => $item->feedback_from,
                'graduate_name' => $item->placement?->talent?->full_name ?? '-',
                'company_name' => $item->placement?->company?->company_name ?? '-',
                'average_score' => $item->average_score,
                'comments' => $item->comments,
                'submitted_at' => optional($item->submitted_at)->toIso8601String(),
            ])->values(),
        ]);
    }

    public function settings()
    {
        return $this->success([
            'summary' => [
                'users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'roles' => Role::count(),
                'batches' => IntakeBatch::count(),
            ],
            'users' => User::with('role')->orderByDesc('id')->limit(20)->get()->map(fn ($item) => [
                'id' => $item->id,
                'full_name' => $item->full_name,
                'email' => $item->email,
                'role' => $item->role?->display_name ?? $item->role?->role_name ?? '-',
                'status' => $item->status,
            ])->values(),
            'roles' => Role::orderBy('sort_order')->get()->map(fn ($item) => [
                'id' => $item->id,
                'role_name' => $item->role_name,
                'display_name' => $item->display_name,
                'is_active' => (bool) $item->is_active,
            ])->values(),
            'batches' => IntakeBatch::orderByDesc('year')->limit(20)->get()->map(fn ($item) => [
                'id' => $item->id,
                'batch_name' => $item->batch_name,
                'year' => $item->year,
                'status' => $item->status,
                'start_date' => optional($item->start_date)->toDateString(),
                'end_date' => optional($item->end_date)->toDateString(),
            ])->values(),
        ]);
    }

    public function graduateRemarks(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $dailyLogRemarks = DailyLog::with('talent')
            ->whereNotNull('admin_remarks')
            ->where('admin_remarks', '!=', '')
            ->get()
            ->map(fn ($item) => [
                'id' => 'daily-log-' . $item->id,
                'graduate_id' => $item->talent?->id_graduan,
                'graduate_name' => $item->talent?->full_name ?? '-',
                'source' => 'Daily Log',
                'title' => 'Admin Daily Log Remark',
                'remark' => $item->admin_remarks,
                'status' => $item->status,
                'date' => optional($item->reviewed_at ?? $item->updated_at)->toDateString(),
            ]);

        $logbookRemarks = LogbookUpload::whereNotNull('komen_mentor')
            ->where('komen_mentor', '!=', '')
            ->get()
            ->map(fn ($item) => [
                'id' => 'logbook-' . $item->id,
                'graduate_id' => $item->id_graduan,
                'graduate_name' => $item->nama_graduan,
                'source' => 'Logbook',
                'title' => 'Mentor Logbook Comment',
                'remark' => $item->komen_mentor,
                'status' => $item->status_semakan,
                'date' => optional($item->tarikh_semakan ?? $item->updated_at)->toDateString(),
            ]);

        $attendanceRemarks = KehadiranPrestasi::with('graduan')
            ->whereNotNull('komen_mentor')
            ->where('komen_mentor', '!=', '')
            ->get()
            ->map(fn ($item) => [
                'id' => 'attendance-' . $item->id,
                'graduate_id' => $item->id_graduan,
                'graduate_name' => $item->graduan?->full_name ?? '-',
                'source' => 'Attendance',
                'title' => 'Attendance Mentor Comment',
                'remark' => $item->komen_mentor,
                'status' => $item->status_logbook,
                'date' => optional($item->updated_at)->toDateString(),
            ]);

        $feedbackRemarks = InternshipFeedback::with('placement.talent')
            ->whereNotNull('comments')
            ->where('comments', '!=', '')
            ->get()
            ->map(fn ($item) => [
                'id' => 'feedback-' . $item->id,
                'graduate_id' => $item->placement?->talent?->id_graduan,
                'graduate_name' => $item->placement?->talent?->full_name ?? '-',
                'source' => 'Feedback',
                'title' => 'Placement Feedback Comment',
                'remark' => $item->comments,
                'status' => $item->feedback_from,
                'date' => optional($item->submitted_at)->toDateString(),
            ]);

        $items = $dailyLogRemarks
            ->concat($logbookRemarks)
            ->concat($attendanceRemarks)
            ->concat($feedbackRemarks)
            ->filter(function ($item) use ($search) {
                if ($search === '') {
                    return true;
                }

                $haystack = strtolower(implode(' ', [
                    $item['graduate_id'] ?? '',
                    $item['graduate_name'] ?? '',
                    $item['source'] ?? '',
                    $item['title'] ?? '',
                    $item['remark'] ?? '',
                ]));

                return str_contains($haystack, strtolower($search));
            })
            ->sortByDesc('date')
            ->values();

        return $this->success([
            'items' => $items,
        ]);
    }

    public function surveys(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $items = TrainingRecord::with('participants')
            ->orderByDesc('tarikh_training')
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'survey_id' => $item->id_training,
                'title' => $item->tajuk_training,
                'company_name' => $item->nama_syarikat,
                'session' => $item->sesi,
                'date' => optional($item->tarikh_training)->toDateString(),
                'respondent_count' => $item->participants->count(),
                'satisfaction_score' => (float) ($item->skor_kepuasan ?? 0),
                'pre_assessment_avg' => (float) ($item->pre_assessment_avg ?? 0),
                'post_assessment_avg' => (float) ($item->post_assessment_avg ?? 0),
                'improvement_pct' => (float) ($item->improvement_pct ?? 0),
                'status' => $item->status,
                'notes' => $item->catatan,
            ])
            ->filter(function ($item) use ($search) {
                if ($search === '') {
                    return true;
                }

                $haystack = strtolower(implode(' ', [
                    $item['survey_id'] ?? '',
                    $item['title'] ?? '',
                    $item['company_name'] ?? '',
                    $item['status'] ?? '',
                ]));

                return str_contains($haystack, strtolower($search));
            })
            ->values();

        return $this->success([
            'items' => $items,
        ]);
    }

    public function clearCache()
    {
        Cache::flush();
        Artisan::call('optimize:clear');

        return $this->success([], 'System cache cleared successfully.');
    }

    public function mindefDashboard()
    {
        $totalGraduates = Talent::whereNotNull('id_graduan')->count();
        $activeGraduates = Talent::whereNotNull('id_graduan')->where('status_aktif', 'Aktif')->count();
        $criticalIssues = IsuRisiko::where('tahap_risiko', 'Kritikal')->whereIn('status', ['Baru', 'Dalam Tindakan'])->count();

        $latestKpi = KpiDashboard::orderByDesc('bulan')->first();

        return $this->success([
            'summary' => [
                'total_graduates' => $totalGraduates,
                'active_graduates' => $activeGraduates,
                'critical_issues' => $criticalIssues,
            ],
            'kpi' => $latestKpi ? [
                'month' => $latestKpi->bulan,
                'year' => $latestKpi->tahun,
                'avg_attendance_pct' => (float) $latestKpi->avg_kehadiran_pct,
                'retention_rate_pct' => (float) $latestKpi->retention_rate_pct,
                'surat_kuning_pct' => (float) $latestKpi->surat_kuning_siap_pct,
                'surat_biru_pct' => (float) $latestKpi->surat_biru_siap_pct,
                'budget_utilization_pct' => (float) $latestKpi->budget_utilization_pct,
                'training_compliance_pct' => (float) $latestKpi->training_compliance_rate_pct,
            ] : null,
        ]);
    }
}
