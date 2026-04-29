<?php

namespace Database\Seeders;

use App\Models\KpiDashboard;
use Illuminate\Database\Seeder;

class KpiDashboardSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['bulan' => 'September 2025', 'tahun' => 2025, 'total_graduan_aktif' => 188, 'total_graduan_tamat_6bulan' => 60, 'graduan_diserap_6bulan' => 48, 'retention_rate_pct' => 80, 'total_bayaran_selesai' => 155, 'total_bayaran_lewat' => 5, 'avg_kehadiran_pct' => 0.93, 'avg_prestasi_score' => 9.1, 'surat_kuning_siap_pct' => 77.9, 'surat_biru_siap_pct' => 86.6, 'logbook_submitted_pct' => 83.3, 'isu_kritikal_active' => 4, 'budget_utilization_pct' => 74.8, 'training_sessions_completed' => 2, 'training_compliance_rate_pct' => 77.2, 'avg_training_satisfaction' => 9.1, 'avg_skill_improvement_pct' => 54.1],
            ['bulan' => 'Oktober 2025', 'tahun' => 2025, 'total_graduan_aktif' => 194, 'total_graduan_tamat_6bulan' => 78, 'graduan_diserap_6bulan' => 51, 'retention_rate_pct' => 65.38, 'total_bayaran_selesai' => 175, 'total_bayaran_lewat' => 9, 'avg_kehadiran_pct' => 0.92, 'avg_prestasi_score' => 9.1, 'surat_kuning_siap_pct' => 74.8, 'surat_biru_siap_pct' => 77.7, 'logbook_submitted_pct' => 85.3, 'isu_kritikal_active' => 4, 'budget_utilization_pct' => 70.6, 'training_sessions_completed' => 4, 'training_compliance_rate_pct' => 92.3, 'avg_training_satisfaction' => 9.4, 'avg_skill_improvement_pct' => 42.4],
            ['bulan' => 'November 2025', 'tahun' => 2025, 'total_graduan_aktif' => 190, 'total_graduan_tamat_6bulan' => 85, 'graduan_diserap_6bulan' => 60, 'retention_rate_pct' => 70.59, 'total_bayaran_selesai' => 168, 'total_bayaran_lewat' => 7, 'avg_kehadiran_pct' => 0.90, 'avg_prestasi_score' => 8.8, 'surat_kuning_siap_pct' => 80.2, 'surat_biru_siap_pct' => 82.1, 'logbook_submitted_pct' => 87.5, 'isu_kritikal_active' => 3, 'budget_utilization_pct' => 78.3, 'training_sessions_completed' => 6, 'training_compliance_rate_pct' => 88.5, 'avg_training_satisfaction' => 8.9, 'avg_skill_improvement_pct' => 38.7],
            ['bulan' => 'Disember 2025', 'tahun' => 2025, 'total_graduan_aktif' => 185, 'total_graduan_tamat_6bulan' => 92, 'graduan_diserap_6bulan' => 68, 'retention_rate_pct' => 73.91, 'total_bayaran_selesai' => 160, 'total_bayaran_lewat' => 12, 'avg_kehadiran_pct' => 0.88, 'avg_prestasi_score' => 8.5, 'surat_kuning_siap_pct' => 83.5, 'surat_biru_siap_pct' => 85.3, 'logbook_submitted_pct' => 82.1, 'isu_kritikal_active' => 5, 'budget_utilization_pct' => 82.1, 'training_sessions_completed' => 8, 'training_compliance_rate_pct' => 85.2, 'avg_training_satisfaction' => 8.7, 'avg_skill_improvement_pct' => 41.2],
            ['bulan' => 'Januari 2026', 'tahun' => 2026, 'total_graduan_aktif' => 182, 'total_graduan_tamat_6bulan' => 100, 'graduan_diserap_6bulan' => 76, 'retention_rate_pct' => 76.0, 'total_bayaran_selesai' => 155, 'total_bayaran_lewat' => 8, 'avg_kehadiran_pct' => 0.91, 'avg_prestasi_score' => 8.9, 'surat_kuning_siap_pct' => 85.1, 'surat_biru_siap_pct' => 88.2, 'logbook_submitted_pct' => 89.5, 'isu_kritikal_active' => 3, 'budget_utilization_pct' => 85.7, 'training_sessions_completed' => 10, 'training_compliance_rate_pct' => 90.1, 'avg_training_satisfaction' => 9.0, 'avg_skill_improvement_pct' => 39.5],
            ['bulan' => 'Februari 2026', 'tahun' => 2026, 'total_graduan_aktif' => 178, 'total_graduan_tamat_6bulan' => 108, 'graduan_diserap_6bulan' => 83, 'retention_rate_pct' => 76.85, 'total_bayaran_selesai' => 150, 'total_bayaran_lewat' => 6, 'avg_kehadiran_pct' => 0.92, 'avg_prestasi_score' => 9.0, 'surat_kuning_siap_pct' => 87.3, 'surat_biru_siap_pct' => 90.5, 'logbook_submitted_pct' => 91.2, 'isu_kritikal_active' => 2, 'budget_utilization_pct' => 88.4, 'training_sessions_completed' => 12, 'training_compliance_rate_pct' => 92.8, 'avg_training_satisfaction' => 9.2, 'avg_skill_improvement_pct' => 43.1],
        ];

        foreach ($data as $row) {
            KpiDashboard::updateOrCreate(
                ['bulan' => $row['bulan'], 'tahun' => $row['tahun']],
                $row
            );
        }
    }
}
