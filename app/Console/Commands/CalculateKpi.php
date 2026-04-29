<?php

namespace App\Console\Commands;

use App\Models\KpiDashboard;
use App\Models\Talent;
use App\Models\KehadiranPrestasi;
use App\Models\KewanganElaun;
use App\Models\StatusSurat;
use App\Models\LogbookUpload;
use App\Models\IsuRisiko;
use App\Models\SyarikatPelaksana;
use App\Models\TrainingRecord;
use App\Models\TrainingParticipant;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateKpi extends Command
{
    protected $signature = 'protege:calculate-kpi {month?} {year?}';
    protected $description = 'Calculate and store monthly KPI snapshot';

    public function handle(): int
    {
        $now = Carbon::now();
        $monthName = $this->argument('month') ?? $now->translatedFormat('F Y');
        $year = (int) ($this->argument('year') ?? $now->year);

        $this->info("Calculating KPI for: {$monthName} {$year}");

        $totalAktif = Talent::where('status_aktif', 'Aktif')->orWhere(function ($q) {
            $q->whereNull('status_aktif')->whereIn('status', ['approved', 'assigned', 'in_progress']);
        })->count();

        $tamat6Bulan = Talent::where('status_penyerapan_6bulan', '!=', 'Belum Layak')
            ->whereNotNull('status_penyerapan_6bulan')->count();
        $diserap = Talent::where('status_penyerapan_6bulan', 'Diserap')->count();
        $retentionRate = $tamat6Bulan > 0 ? round(($diserap / $tamat6Bulan) * 100, 2) : 0;

        $bayaranSelesai = KewanganElaun::where('status_bayaran', 'Selesai')->count();
        $bayaranLewat = KewanganElaun::where('status_bayaran', 'Lewat')->count();

        $avgKehadiran = KehadiranPrestasi::avg('kehadiran_pct') ?? 0;
        $avgPrestasi = KehadiranPrestasi::avg('skor_prestasi') ?? 0;

        $totalSK = StatusSurat::where('jenis_surat', 'Surat Kuning')->count();
        $skSiap = StatusSurat::where('jenis_surat', 'Surat Kuning')->where('status_surat', 'Selesai')->count();
        $totalSB = StatusSurat::where('jenis_surat', 'Surat Biru')->count();
        $sbSiap = StatusSurat::where('jenis_surat', 'Surat Biru')->where('status_surat', 'Selesai')->count();

        $totalLogExpected = LogbookUpload::count();
        $logSubmitted = LogbookUpload::whereIn('status_logbook', ['Dikemukakan', 'Dalam Semakan'])->count();

        $isuKritikal = IsuRisiko::where('tahap_risiko', 'Kritikal')
            ->whereIn('status', ['Baru', 'Dalam Tindakan'])->count();

        $totalAllocated = SyarikatPelaksana::sum('peruntukan_diluluskan');
        $totalUsed = SyarikatPelaksana::sum('peruntukan_diguna');
        $budgetUtil = $totalAllocated > 0 ? round(($totalUsed / $totalAllocated) * 100, 2) : 0;

        $trainingCompleted = TrainingRecord::where('status', 'Selesai')->count();
        $avgCompliance = \App\Models\SyarikatPenempatan::avg('training_compliance_pct') ?? 0;
        $avgSatisfaction = TrainingRecord::where('status', 'Selesai')->avg('skor_kepuasan') ?? 0;
        $avgImprovement = TrainingParticipant::avg('improvement_pct') ?? 0;

        KpiDashboard::updateOrCreate(
            ['bulan' => $monthName, 'tahun' => $year],
            [
                'total_graduan_aktif' => $totalAktif,
                'total_graduan_tamat_6bulan' => $tamat6Bulan,
                'graduan_diserap_6bulan' => $diserap,
                'retention_rate_pct' => $retentionRate,
                'total_bayaran_selesai' => $bayaranSelesai,
                'total_bayaran_lewat' => $bayaranLewat,
                'avg_kehadiran_pct' => $avgKehadiran,
                'avg_prestasi_score' => $avgPrestasi,
                'surat_kuning_siap_pct' => $totalSK > 0 ? round(($skSiap / $totalSK) * 100, 2) : 0,
                'surat_biru_siap_pct' => $totalSB > 0 ? round(($sbSiap / $totalSB) * 100, 2) : 0,
                'logbook_submitted_pct' => $totalLogExpected > 0 ? round(($logSubmitted / $totalLogExpected) * 100, 2) : 0,
                'isu_kritikal_active' => $isuKritikal,
                'budget_utilization_pct' => $budgetUtil,
                'training_sessions_completed' => $trainingCompleted,
                'training_compliance_rate_pct' => $avgCompliance,
                'avg_training_satisfaction' => $avgSatisfaction,
                'avg_skill_improvement_pct' => $avgImprovement,
            ]
        );

        $this->info('KPI snapshot saved.');
        return self::SUCCESS;
    }
}
