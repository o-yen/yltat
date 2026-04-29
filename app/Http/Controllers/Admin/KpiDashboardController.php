<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KpiDashboard;
use Illuminate\Http\Request;

class KpiDashboardController extends Controller
{
    public function index(Request $request)
    {
        $records = KpiDashboard::orderByDesc('tahun')->orderByDesc('id')->get();
        $latest = $records->first();

        // KPI targets for comparison
        $targets = [
            'retention_rate_pct' => 75,
            'avg_kehadiran_pct' => 0.85,
            'avg_prestasi_score' => 7.5,
            'surat_kuning_siap_pct' => 90,
            'surat_biru_siap_pct' => 85,
            'logbook_submitted_pct' => 90,
            'budget_utilization_pct' => [80, 95],
            'training_compliance_rate_pct' => 85,
            'avg_training_satisfaction' => 8.5,
            'avg_skill_improvement_pct' => 35,
        ];

        return view('admin.kpi-dashboard.index', compact('records', 'latest', 'targets'));
    }
}
