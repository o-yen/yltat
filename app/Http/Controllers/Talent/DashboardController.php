<?php

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use Illuminate\Support\Facades\Auth;

class DashboardController extends BaseTalentController
{
    public function index()
    {
        $talent = $this->getTalent();

        $activePlacement = $talent->activePlacement;

        $recentLogs = $talent->dailyLogs()
            ->orderByDesc('log_date')
            ->limit(5)
            ->get();

        $totalLogs = $talent->dailyLogs()->count();

        $thisMonthLogs = $talent->dailyLogs()
            ->whereYear('log_date', now()->year)
            ->whereMonth('log_date', now()->month)
            ->count();

        $todayLogged = $talent->dailyLogs()
            ->whereDate('log_date', today())
            ->exists();

        $latestAllowance = $talent->budgetTransactions()
            ->where('category', 'stipend')
            ->orderByDesc('transaction_date')
            ->first();

        return view('talent.dashboard', compact(
            'talent',
            'activePlacement',
            'recentLogs',
            'totalLogs',
            'thisMonthLogs',
            'todayLogged',
            'latestAllowance'
        ));
    }
}
