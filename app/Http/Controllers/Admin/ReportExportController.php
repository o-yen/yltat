<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KpiDashboard;
use App\Models\SyarikatPelaksana;
use App\Models\SyarikatPenempatan;
use App\Models\Talent;
use App\Models\KehadiranPrestasi;
use App\Models\KewanganElaun;
use App\Models\StatusSurat;
use App\Models\LogbookUpload;
use App\Models\IsuRisiko;
use App\Models\TrainingRecord;
use App\Models\TrainingParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportExportController extends Controller
{
    /**
     * Monthly Executive Report
     */
    public function executive(Request $request)
    {
        $latestKpi = KpiDashboard::orderByDesc('tahun')->orderByDesc('id')->first();
        $kpiHistory = KpiDashboard::orderBy('tahun')->orderBy('id')->get();
        $pelaksana = SyarikatPelaksana::all();
        $activeIssues = IsuRisiko::whereIn('status', ['Baru', 'Dalam Tindakan'])
            ->orderByDesc('tarikh_isu')->limit(20)->get();
        $totalGraduan = Talent::count();
        $totalAktif = Talent::where('status_aktif', 'Aktif')->count();

        return view('admin.reports.export-executive', compact(
            'latestKpi', 'kpiHistory', 'pelaksana', 'activeIssues', 'totalGraduan', 'totalAktif'
        ));
    }

    /**
     * Company Performance Report
     */
    public function company(Request $request)
    {
        $pelaksanaId = $request->get('id_pelaksana');
        $pelaksana = $pelaksanaId
            ? SyarikatPelaksana::findOrFail($pelaksanaId)
            : SyarikatPelaksana::first();

        $pelaksana->loadCount('graduan');
        $suratRecords = StatusSurat::where('id_pelaksana', $pelaksana->id_pelaksana)->get();
        $kewanganRecords = KewanganElaun::where('id_pelaksana', $pelaksana->id_pelaksana)->get();
        $graduates = Talent::where('id_pelaksana', $pelaksana->id_pelaksana)->get();

        return view('admin.reports.export-company', compact(
            'pelaksana', 'suratRecords', 'kewanganRecords', 'graduates'
        ));
    }

    /**
     * Participant Progress Report
     */
    public function participant(Request $request)
    {
        $talentId = $request->get('talent_id');
        $talent = $talentId
            ? Talent::findOrFail($talentId)
            : Talent::whereNotNull('id_graduan')->first();

        $talent->load(['syarikatPelaksana', 'syarikatPenempatan']);
        $kehadiran = KehadiranPrestasi::where('id_graduan', $talent->id_graduan ?? $talent->talent_code)
            ->orderBy('tahun')->orderBy('bulan')->get();
        $logbooks = LogbookUpload::where('id_graduan', $talent->id_graduan ?? $talent->talent_code)
            ->orderBy('tahun')->orderBy('bulan')->get();

        return view('admin.reports.export-participant', compact('talent', 'kehadiran', 'logbooks'));
    }

    /**
     * Training Effectiveness Report
     */
    public function training(Request $request)
    {
        $records = TrainingRecord::with('syarikatPenempatan')
            ->orderByDesc('tarikh_training')->get();
        $participants = TrainingParticipant::all();

        $avgImprovement = $participants->avg('improvement_pct');
        $avgSatisfaction = $records->where('status', 'Selesai')->avg('skor_kepuasan');
        $totalBudget = $records->sum('budget_allocated');
        $totalSpent = $records->sum('budget_spent');

        return view('admin.reports.export-training', compact(
            'records', 'participants', 'avgImprovement', 'avgSatisfaction', 'totalBudget', 'totalSpent'
        ));
    }
}
