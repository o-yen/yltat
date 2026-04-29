<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Talent;
use App\Models\Company;
use App\Models\Placement;
use App\Models\BudgetAllocation;
use App\Models\BudgetTransaction;
use App\Models\AuditLog;
use App\Models\KpiDashboard;
use App\Models\SyarikatPelaksana;
use App\Models\SyarikatPenempatan;
use App\Models\KehadiranPrestasi;
use App\Models\KewanganElaun;
use App\Models\StatusSurat;
use App\Models\LogbookUpload;
use App\Models\IsuRisiko;
use App\Models\TrainingRecord;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role?->role_name;

        // Route to role-specific dashboard
        if (in_array($role, ['syarikat_pelaksana'])) {
            return $this->pelaksanaDashboard($user);
        }
        if (in_array($role, ['rakan_kolaborasi'])) {
            return $this->rakanKolaborasiDashboard($user);
        }

        // Default: Executive dashboard for PMO, MINDEF, super_admin, etc.
        return $this->executiveDashboard();
    }

    /**
     * Executive Dashboard — PMO, MINDEF, super_admin, programme_admin
     */
    protected function executiveDashboard()
    {
        // Latest KPI snapshot
        $latestKpi = KpiDashboard::orderByDesc('tahun')->orderByDesc('id')->first();
        $kpiHistory = KpiDashboard::orderBy('tahun')->orderBy('id')->get();

        // Top-level KPI cards
        $totalGraduanAktif = Talent::where(function ($query) {
            $query->where('status_aktif', 'Aktif')
                ->orWhere(function ($subQuery) {
                    $subQuery->whereNull('status_aktif')
                        ->where('status', 'Aktif');
                });
        })->count()
            ?: ($latestKpi->total_graduan_aktif ?? 0);

        // Surat completion
        $totalSuratKuning = StatusSurat::where('jenis_surat', 'Surat Kuning')->count();
        $suratKuningSelesai = StatusSurat::where('jenis_surat', 'Surat Kuning')->where('status_surat', 'Selesai')->count();
        $totalSuratBiru = StatusSurat::where('jenis_surat', 'Surat Biru')->count();
        $suratBiruSelesai = StatusSurat::where('jenis_surat', 'Surat Biru')->where('status_surat', 'Selesai')->count();

        // Graduate distribution by category
        $kategoriData = Talent::select('kategori', DB::raw('COUNT(*) as count'))
            ->whereNotNull('kategori')
            ->groupBy('kategori')
            ->get()
            ->map(fn ($item) => [
                'label' => $item->kategori,
                'value' => (int) $item->count,
            ])
            ->values();

        // Graduates per placement company
        $graduanPerPenempatan = SyarikatPenempatan::select('nama_syarikat', 'jumlah_graduan_ditempatkan')
            ->orderByDesc('jumlah_graduan_ditempatkan')
            ->get();

        // Monthly attendance trend
        $kehadiranTrend = KehadiranPrestasi::select('bulan', DB::raw('AVG(kehadiran_pct) as avg_kehadiran'))
            ->groupBy('bulan')
            ->get()
            ->sortBy(fn ($row) => $this->monthSortKey($row->bulan))
            ->map(function ($row) {
                $row->bulan = $this->displayMonthLabel($row->bulan);
                return $row;
            })
            ->values();

        // Active issues
        $activeIssues = IsuRisiko::whereIn('status', ['Baru', 'Dalam Tindakan'])
            ->orderByDesc('tarikh_isu')
            ->limit(10)
            ->get();
        $kritikalCount = IsuRisiko::where('tahap_risiko', 'Kritikal')
            ->whereIn('status', ['Baru', 'Dalam Tindakan'])
            ->count();

        // Syarikat Pelaksana summary
        $pelaksanaSummary = SyarikatPelaksana::all();

        // Sector/Industry distribution — count placed PROTEGE graduates by placement company sector
        $sektorData = Talent::query()
            ->with(['syarikatPenempatan:id_syarikat,sektor_industri'])
            ->whereNotNull('id_graduan')
            ->whereNotNull('id_syarikat_penempatan')
            ->get(['id_graduan', 'id_syarikat_penempatan'])
            ->groupBy(function ($talent) {
                $sector = trim((string) data_get($talent, 'syarikatPenempatan.sektor_industri'));
                return $sector !== '' ? $sector : 'Tidak Ditetapkan';
            })
            ->map(function ($talents, $sector) {
                return (object) [
                    'sektor_industri' => $sector,
                    'total_graduan' => $talents->pluck('id_graduan')->filter()->unique()->count(),
                ];
            })
            ->sortByDesc('total_graduan')
            ->values();

        // Budget comparison per Pelaksana (kuota vs used)
        $budgetComparison = SyarikatPelaksana::select('nama_syarikat', 'peruntukan_diluluskan', 'peruntukan_diguna', 'baki_peruntukan')
            ->orderBy('nama_syarikat')
            ->get();

        // Graduate tracker — pull from kehadiran records that have actual data
        $recentKehadiran = KehadiranPrestasi::select('id_graduan', 'id_syarikat',
                DB::raw('AVG(kehadiran_pct) as avg_kehadiran'),
                DB::raw('AVG(skor_prestasi) as avg_skor'),
                DB::raw('MAX(status_logbook) as last_logbook'))
            ->groupBy('id_graduan', 'id_syarikat')
            ->orderByDesc(DB::raw('MAX(id)'))
            ->limit(10)
            ->get();

        $graduateTracker = $recentKehadiran->map(function ($kh) {
            $talent = Talent::where('id_graduan', $kh->id_graduan)->first();
            $penempatan = SyarikatPenempatan::find($kh->id_syarikat);
            return (object) [
                'id' => $talent?->id,
                'full_name' => $talent?->full_name ?? $kh->id_graduan,
                'id_graduan' => $kh->id_graduan,
                'status_aktif' => $talent?->status_aktif ?? $talent?->status ?? '-',
                'company_name' => $penempatan?->nama_syarikat ?? $kh->id_syarikat ?? '-',
                'latest_kehadiran' => $kh->avg_kehadiran,
                'latest_skor' => round($kh->avg_skor, 1),
                'latest_logbook' => $kh->last_logbook,
            ];
        });

        // Monthly trend summary cards
        $avgKehadiranAll = KehadiranPrestasi::avg('kehadiran_pct');
        $avgPrestasiAll = KehadiranPrestasi::avg('skor_prestasi');
        $avgComplianceAll = SyarikatPenempatan::avg('training_compliance_pct');

        // Payment summary
        $totalBayaranSelesai = KewanganElaun::where('status_bayaran', 'Selesai')->count();
        $totalBayaranLewat = KewanganElaun::where('status_bayaran', 'Lewat')->count();

        // Recent activity
        $recentActivity = AuditLog::with('user:id,full_name')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Legacy data for backward compatibility
        $totalTalents = Talent::count();
        $activePlacements = Placement::whereIn('placement_status', ['active', 'confirmed'])->count();
        $totalCompanies = Company::where('status', 'active')->count();
        $currentYear = date('Y');
        $totalAllocated = BudgetAllocation::where('fiscal_year', $currentYear)->sum('allocated_amount');
        $totalSpent = BudgetTransaction::where('status', 'approved')->whereYear('transaction_date', $currentYear)->sum('amount');
        $remainingBudget = $totalAllocated - $totalSpent;

        return view('admin.dashboard', compact(
            'latestKpi', 'kpiHistory',
            'totalGraduanAktif',
            'totalSuratKuning', 'suratKuningSelesai',
            'totalSuratBiru', 'suratBiruSelesai',
            'kategoriData', 'graduanPerPenempatan',
            'kehadiranTrend',
            'activeIssues', 'kritikalCount',
            'pelaksanaSummary',
            'totalBayaranSelesai', 'totalBayaranLewat',
            'recentActivity',
            'totalTalents', 'activePlacements', 'totalCompanies',
            'totalAllocated', 'totalSpent', 'remainingBudget',
            'sektorData', 'budgetComparison', 'graduateTracker',
            'avgKehadiranAll', 'avgPrestasiAll', 'avgComplianceAll'
        ));
    }

    /**
     * Company Performance Dashboard — for Syarikat Pelaksana role
     */
    protected function pelaksanaDashboard($user)
    {
        $pelaksanaId = $user->id_pelaksana;
        $pelaksana = SyarikatPelaksana::find($pelaksanaId);

        $graduanCount = Talent::where('id_pelaksana', $pelaksanaId)->count();
        $kewanganRecords = KewanganElaun::where('id_pelaksana', $pelaksanaId)->orderByDesc('id')->limit(20)->get();
        $suratRecords = StatusSurat::where('id_pelaksana', $pelaksanaId)->orderByDesc('created_at')->limit(20)->get();
        $bayaranSelesai = KewanganElaun::where('id_pelaksana', $pelaksanaId)->where('status_bayaran', 'Selesai')->count();
        $bayaranLewat = KewanganElaun::where('id_pelaksana', $pelaksanaId)->where('status_bayaran', 'Lewat')->count();

        return view('admin.dashboard-pelaksana', compact(
            'pelaksana', 'graduanCount', 'kewanganRecords', 'suratRecords',
            'bayaranSelesai', 'bayaranLewat'
        ));
    }

    /**
     * Participant Tracking Dashboard — for Rakan Kolaborasi role
     */
    protected function rakanKolaborasiDashboard($user)
    {
        $syarikatId = $user->id_syarikat_penempatan;
        $syarikat = SyarikatPenempatan::find($syarikatId);

        $graduanCount = Talent::where('id_syarikat_penempatan', $syarikatId)->count();
        $kehadiranRecords = KehadiranPrestasi::where('id_syarikat', $syarikatId)->orderByDesc('id')->limit(20)->get();
        $logbookRecords = LogbookUpload::where('id_syarikat', $syarikatId)->orderByDesc('id')->limit(20)->get();
        $trainingRecords = TrainingRecord::where('id_syarikat', $syarikatId)->get();
        $avgKehadiran = KehadiranPrestasi::where('id_syarikat', $syarikatId)->avg('kehadiran_pct');
        $avgPrestasi = KehadiranPrestasi::where('id_syarikat', $syarikatId)->avg('skor_prestasi');

        return view('admin.dashboard-rakan', compact(
            'syarikat', 'graduanCount', 'kehadiranRecords', 'logbookRecords',
            'trainingRecords', 'avgKehadiran', 'avgPrestasi'
        ));
    }

    protected function monthSortKey($value): int|string
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        $normalized = Str::of((string) $value)
            ->trim()
            ->lower()
            ->ascii()
            ->replaceMatches('/\s+/', ' ')
            ->value();

        $monthMap = [
            'january' => 1,
            'januari' => 1,
            'february' => 2,
            'februari' => 2,
            'march' => 3,
            'mac' => 3,
            'april' => 4,
            'may' => 5,
            'mei' => 5,
            'june' => 6,
            'jun' => 6,
            'july' => 7,
            'julai' => 7,
            'august' => 8,
            'ogos' => 8,
            'september' => 9,
            'october' => 10,
            'oktober' => 10,
            'november' => 11,
            'december' => 12,
            'disember' => 12,
        ];

        if (preg_match('/^([a-z]+)\s+(\d{4})$/', $normalized, $matches) && isset($monthMap[$matches[1]])) {
            return ((int) $matches[2] * 100) + $monthMap[$matches[1]];
        }

        if (preg_match('/^(\d{4})-(\d{1,2})$/', $normalized, $matches)) {
            return ((int) $matches[1] * 100) + (int) $matches[2];
        }

        if (preg_match('/^(\d{1,2})[\/-](\d{4})$/', $normalized, $matches)) {
            return ((int) $matches[2] * 100) + (int) $matches[1];
        }

        return $normalized;
    }

    protected function displayMonthLabel($value): string
    {
        if (is_numeric($value)) {
            return Carbon::create()->month((int) $value)->locale(app()->getLocale())->translatedFormat('F');
        }

        return (string) $value;
    }
}
