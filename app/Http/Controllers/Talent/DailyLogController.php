<?php

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use App\Models\Talent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyLogController extends Controller
{
    private function getTalent()
    {
        $user = Auth::user();
        $talent = $user->talent;

        if (! $talent) {
            $talent = Talent::whereHas('linkedUser', function ($query) use ($user) {
                $query->where('id', $user->id);
            })->first();
        }

        if (! $talent && $user?->email) {
            $talent = Talent::where('email', $user->email)->first();
        }

        if (! $talent) {
            abort(403, __('messages.talent_record_not_found'));
        }

        return $talent;
    }

    public function index(Request $request)
    {
        $talent = $this->getTalent();

        $query = $talent->dailyLogs()->orderByDesc('log_date');

        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('log_date', $year)->whereMonth('log_date', $month);
        }

        $logs = $query->paginate(15)->withQueryString();

        $todayLogged = $talent->dailyLogs()->whereDate('log_date', today())->exists();

        return view('talent.daily-logs.index', compact('logs', 'todayLogged'));
    }

    public function create()
    {
        $talent = $this->getTalent();

        if ($talent->dailyLogs()->whereDate('log_date', today())->exists()) {
            return redirect()->route('talent.daily-logs.index')
                ->with('error', __('messages.daily_log_already_recorded_today'));
        }

        return view('talent.daily-logs.create');
    }

    public function store(Request $request)
    {
        $talent = $this->getTalent();

        $request->validate([
            'log_date'   => 'required|date|before_or_equal:today',
            'activities' => 'required|string|max:3000',
            'challenges' => 'nullable|string|max:1500',
            'learnings'  => 'nullable|string|max:1500',
            'mood'       => 'required|in:great,good,neutral,tired,difficult',
        ]);

        $exists = $talent->dailyLogs()->whereDate('log_date', $request->log_date)->exists();

        if ($exists) {
            return back()->withErrors(['log_date' => __('messages.daily_log_for_date_exists')])->withInput();
        }

        $talent->dailyLogs()->create([
            'placement_id' => $talent->activePlacement?->id,
            'log_date'     => $request->log_date,
            'activities'   => $request->activities,
            'challenges'   => $request->challenges,
            'learnings'    => $request->learnings,
            'mood'         => $request->mood,
            'status'       => 'submitted',
        ]);

        return redirect()->route('talent.daily-logs.index')
            ->with('success', __('messages.daily_log_saved'));
    }

    public function show(DailyLog $dailyLog)
    {
        $this->authorizeLog($dailyLog);

        return view('talent.daily-logs.show', compact('dailyLog'));
    }

    public function edit(DailyLog $dailyLog)
    {
        $this->authorizeLog($dailyLog);

        return view('talent.daily-logs.edit', compact('dailyLog'));
    }

    public function update(Request $request, DailyLog $dailyLog)
    {
        $this->authorizeLog($dailyLog);

        $request->validate([
            'activities' => 'required|string|max:3000',
            'challenges' => 'nullable|string|max:1500',
            'learnings'  => 'nullable|string|max:1500',
            'mood'       => 'required|in:great,good,neutral,tired,difficult',
        ]);

        $dailyLog->update([
            'activities' => $request->activities,
            'challenges' => $request->challenges,
            'learnings'  => $request->learnings,
            'mood'       => $request->mood,
        ]);

        return redirect()->route('talent.daily-logs.show', $dailyLog)
            ->with('success', __('messages.daily_log_updated'));
    }

    public function destroy(DailyLog $dailyLog)
    {
        $this->authorizeLog($dailyLog);

        $dailyLog->delete();

        return redirect()->route('talent.daily-logs.index')
            ->with('success', __('messages.daily_log_deleted'));
    }

    private function authorizeLog(DailyLog $dailyLog): void
    {
        $user = Auth::user();
        $talent = $this->getTalent();
        $dailyLog->loadMissing(['talent.linkedUser', 'placement.talent.linkedUser']);

        $ownsLog = $dailyLog->talent_id === $talent->id
            || $dailyLog->placement?->talent_id === $talent->id
            || $dailyLog->talent?->linkedUser?->id === $user->id
            || $dailyLog->placement?->talent?->linkedUser?->id === $user->id
            || ($user?->email && $dailyLog->talent?->email === $user->email)
            || ($user?->email && $dailyLog->placement?->talent?->email === $user->email);

        if (! $ownsLog) {
            abort(403);
        }
    }
}
