<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use App\Models\Talent;
use App\Models\AuditLog;
use App\Models\SyarikatPelaksana;
use App\Models\SyarikatPenempatan;
use Illuminate\Http\Request;

class DailyLogController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyLog::with(['talent.syarikatPelaksana', 'talent.syarikatPenempatan', 'placement']);

        if ($request->filled('talent_id')) {
            $query->where('talent_id', $request->talent_id);
        }

        if ($request->filled('id_pelaksana')) {
            $query->whereHas('talent', function ($q) use ($request) {
                $q->where('id_pelaksana', $request->id_pelaksana);
            });
        }

        if ($request->filled('id_syarikat_penempatan')) {
            $query->whereHas('talent', function ($q) use ($request) {
                $q->where('id_syarikat_penempatan', $request->id_syarikat_penempatan);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('log_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('log_date', '<=', $request->date_to);
        }

        if ($request->filled('review_status')) {
            if ($request->review_status === 'reviewed') {
                $query->whereNotNull('reviewed_at');
            } else {
                $query->whereNull('reviewed_at');
            }
        }

        $records = $query->orderByDesc('log_date')->orderByDesc('id')->paginate(20)->withQueryString();

        $talents = Talent::orderBy('full_name')
            ->select('id', 'full_name', 'id_graduan')
            ->get();

        $pelaksanas = SyarikatPelaksana::orderBy('nama_syarikat')
            ->select('id_pelaksana', 'nama_syarikat')
            ->get();

        $penempatans = SyarikatPenempatan::orderBy('nama_syarikat')
            ->select('id_syarikat', 'nama_syarikat')
            ->get();

        return view('admin.daily-logs.index', compact('records', 'talents', 'pelaksanas', 'penempatans'));
    }

    public function show(DailyLog $dailyLog)
    {
        $dailyLog->load(['talent', 'placement']);
        return view('admin.daily-logs.show', compact('dailyLog'));
    }

    public function review(Request $request, DailyLog $dailyLog)
    {
        $validated = $request->validate([
            'admin_remarks' => 'nullable|string|max:2000',
        ]);

        $oldData = $dailyLog->toArray();

        $dailyLog->update([
            'admin_remarks' => $validated['admin_remarks'],
            'reviewed_by'   => auth()->user()->name,
            'reviewed_at'   => now(),
        ]);

        AuditLog::log('daily_logs', 'review', $dailyLog->id, $oldData, $dailyLog->fresh()->toArray());

        return redirect()->route('admin.daily-logs.show', $dailyLog)
            ->with('success', __('messages.daily_log_reviewed'));
    }
}
