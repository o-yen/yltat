<?php

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AllowanceController extends BaseTalentController
{
    public function index()
    {
        $talent = $this->getTalent();

        $transactions = $talent->budgetTransactions()
            ->orderByDesc('transaction_date')
            ->get();

        $totalApproved = $transactions->where('status', 'approved')->sum('amount');
        $totalPending  = $transactions->where('status', 'pending')->sum('amount');

        $activePlacement = $talent->activePlacement;
        $monthlyStipend  = $activePlacement?->monthly_stipend ?? 0;

        return view('talent.allowance.index', compact(
            'talent',
            'transactions',
            'totalApproved',
            'totalPending',
            'activePlacement',
            'monthlyStipend'
        ));
    }
}
