<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiDashboard extends Model
{
    use HasFactory;

    protected $table = 'kpi_dashboard';

    protected $fillable = [
        'bulan', 'tahun',
        'total_graduan_aktif', 'total_graduan_tamat_6bulan', 'graduan_diserap_6bulan',
        'retention_rate_pct',
        'total_bayaran_selesai', 'total_bayaran_lewat',
        'avg_kehadiran_pct', 'avg_prestasi_score',
        'surat_kuning_siap_pct', 'surat_biru_siap_pct',
        'logbook_submitted_pct', 'isu_kritikal_active',
        'budget_utilization_pct',
        'training_sessions_completed', 'training_compliance_rate_pct',
        'avg_training_satisfaction', 'avg_skill_improvement_pct',
    ];

    protected $casts = [
        'retention_rate_pct' => 'decimal:2',
        'avg_kehadiran_pct' => 'decimal:2',
        'avg_prestasi_score' => 'decimal:2',
        'surat_kuning_siap_pct' => 'decimal:2',
        'surat_biru_siap_pct' => 'decimal:2',
        'logbook_submitted_pct' => 'decimal:2',
        'budget_utilization_pct' => 'decimal:2',
        'training_compliance_rate_pct' => 'decimal:2',
        'avg_training_satisfaction' => 'decimal:2',
        'avg_skill_improvement_pct' => 'decimal:2',
    ];
}
