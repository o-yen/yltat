<?php

namespace App\Http\Controllers\Company;

class FinanceController extends BaseCompanyController
{
    public function index()
    {
        $company = $this->getCompany();

        // Budget allocations grouped by batch/fiscal year
        $allocations = $company->budgetAllocations()
            ->with('batch')
            ->orderByDesc('fiscal_year')
            ->get();

        $totalAllocated = $allocations->sum('allocated_amount');

        // All disbursement transactions for this company
        $transactions = $company->budgetTransactions()
            ->with('placement.talent')
            ->orderByDesc('transaction_date')
            ->get();

        $totalDisbursed = $transactions->where('status', 'approved')->sum('amount');
        $totalPending   = $transactions->where('status', 'pending')->sum('amount');
        $totalRejected  = $transactions->where('status', 'rejected')->sum('amount');
        $remaining      = $totalAllocated - $totalDisbursed;

        // Per-placement finance summary
        $placements = $company->placements()
            ->with(['talent', 'budgetTransactions'])
            ->whereIn('placement_status', ['active', 'confirmed', 'completed'])
            ->orderByDesc('start_date')
            ->get();

        return view('company.finance.index', compact(
            'company',
            'allocations',
            'totalAllocated',
            'transactions',
            'totalDisbursed',
            'totalPending',
            'totalRejected',
            'remaining',
            'placements'
        ));
    }
}
