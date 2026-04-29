<?php

namespace App\Services;

use App\Models\IsuRisiko;
use App\Models\KewanganElaun;
use App\Models\KehadiranPrestasi;
use App\Models\SyarikatPelaksana;
use App\Models\StatusSurat;
use App\Models\LogbookUpload;
use Carbon\Carbon;

class AutoFlaggingService
{
    protected int $issuesCreated = 0;

    /**
     * Run all auto-flagging rules.
     */
    public function runAll(): int
    {
        $this->issuesCreated = 0;

        $this->checkPaymentOverdue();
        $this->checkLowAttendance();
        $this->checkBudgetOverrun();
        $this->checkDocumentDelay();
        $this->checkLowPerformance();
        $this->checkLogbookLate();

        return $this->issuesCreated;
    }

    /**
     * Rule 1: Payment overdue — hari_lewat > 7
     */
    public function checkPaymentOverdue(): void
    {
        $overduePayments = KewanganElaun::where('status_bayaran', 'Lewat')
            ->where('hari_lewat', '>', 7)
            ->get();

        foreach ($overduePayments as $payment) {
            $this->createIssueIfNotExists(
                "Bayaran Lewat - {$payment->id_graduan} ({$payment->bulan})",
                'Bayaran Lewat',
                "Bayaran elaun untuk {$payment->id_graduan} lewat {$payment->hari_lewat} hari untuk bulan {$payment->bulan}",
                'Tinggi',
                $payment->id_pelaksana,
                null
            );
        }
    }

    /**
     * Rule 2: Low attendance — <75% for 2 consecutive months
     */
    public function checkLowAttendance(): void
    {
        $lowAttendance = KehadiranPrestasi::where('kehadiran_pct', '<', 0.75)
            ->select('id_graduan', 'id_pelaksana', 'id_syarikat')
            ->groupBy('id_graduan', 'id_pelaksana', 'id_syarikat')
            ->havingRaw('COUNT(*) >= 2')
            ->get();

        foreach ($lowAttendance as $record) {
            $this->createIssueIfNotExists(
                "Kehadiran Rendah - {$record->id_graduan}",
                'Kehadiran Rendah',
                "Graduan {$record->id_graduan} kehadiran bawah 75% untuk 2 bulan atau lebih berturut-turut",
                'Sederhana',
                $record->id_pelaksana,
                $record->id_syarikat
            );
        }
    }

    /**
     * Rule 3: Budget overrun — baki_peruntukan < 0
     */
    public function checkBudgetOverrun(): void
    {
        $overrun = SyarikatPelaksana::where('baki_peruntukan', '<', 0)->get();

        foreach ($overrun as $company) {
            $this->createIssueIfNotExists(
                "Budget Overrun - {$company->nama_syarikat}",
                'Bayaran Lewat',
                "Baki peruntukan {$company->nama_syarikat} adalah negatif (RM " . number_format($company->baki_peruntukan, 2) . ")",
                'Kritikal',
                $company->id_pelaksana,
                null
            );
        }
    }

    /**
     * Rule 4: Document delay — Status_Surat not "Selesai" after 14 days
     */
    public function checkDocumentDelay(): void
    {
        $delayed = StatusSurat::where('status_surat', '!=', 'Selesai')
            ->whereNotNull('tarikh_mula_proses')
            ->whereDate('tarikh_mula_proses', '<=', Carbon::now()->subDays(14))
            ->get();

        foreach ($delayed as $surat) {
            $this->createIssueIfNotExists(
                "Surat Lewat - {$surat->jenis_surat} {$surat->id_graduan}",
                'Isu Pematuhan',
                "{$surat->jenis_surat} untuk graduan {$surat->id_graduan} belum selesai selepas 14 hari (Status: {$surat->status_surat})",
                'Sederhana',
                $surat->id_pelaksana,
                null
            );
        }
    }

    /**
     * Rule 5: Low performance — skor_prestasi < 6
     */
    public function checkLowPerformance(): void
    {
        $lowPerf = KehadiranPrestasi::where('skor_prestasi', '<', 6)
            ->orderByDesc('tahun')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        foreach ($lowPerf as $record) {
            $this->createIssueIfNotExists(
                "Prestasi Rendah - {$record->id_graduan} ({$record->bulan})",
                'Prestasi Lemah',
                "Graduan {$record->id_graduan} mendapat skor prestasi {$record->skor_prestasi}/10 untuk {$record->bulan}",
                'Sederhana',
                $record->id_pelaksana,
                $record->id_syarikat
            );
        }
    }

    /**
     * Rule 6: Logbook late — not submitted past deadline
     */
    public function checkLogbookLate(): void
    {
        $late = LogbookUpload::where('status_logbook', 'Belum Dikemukakan')
            ->get();

        foreach ($late as $log) {
            $this->createIssueIfNotExists(
                "Logbook Lewat - {$log->id_graduan} ({$log->bulan})",
                'Logbook Lewat',
                "Logbook graduan {$log->id_graduan} untuk {$log->bulan} belum dikemukakan",
                'Rendah',
                null,
                $log->id_syarikat
            );
        }
    }

    /**
     * Create issue only if a similar one doesn't already exist (by butiran matching).
     */
    protected function createIssueIfNotExists(string $reference, string $kategori, string $butiran, string $tahap, ?string $pelaksana, ?string $syarikat): void
    {
        $exists = IsuRisiko::where('kategori_isu', $kategori)
            ->where('butiran_isu', $butiran)
            ->whereIn('status', ['Baru', 'Dalam Tindakan'])
            ->exists();

        if (!$exists) {
            IsuRisiko::create([
                'id_isu' => IsuRisiko::generateId(),
                'tarikh_isu' => now()->toDateString(),
                'id_pelaksana' => $pelaksana,
                'id_syarikat' => $syarikat,
                'kategori_isu' => $kategori,
                'butiran_isu' => $butiran,
                'tahap_risiko' => $tahap,
                'status' => 'Baru',
                'pic' => 'Auto-Flag System',
            ]);

            $this->issuesCreated++;
        }
    }
}
