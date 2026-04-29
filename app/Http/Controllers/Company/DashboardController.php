<?php

namespace App\Http\Controllers\Company;

class DashboardController extends BaseCompanyController
{
    public function index()
    {
        $company = $this->getCompany();

        $placements = $company->placements()->with('talent')->get();

        $activePlacements   = $placements->whereIn('placement_status', ['active', 'confirmed']);
        $completedPlacements = $placements->where('placement_status', 'completed');

        $pendingFeedbackCount = $activePlacements->filter(function ($placement) {
            return $placement->feedback()->where('feedback_from', 'company')->doesntExist();
        })->count();

        $totalAllocated = $company->budgetAllocations()->sum('allocated_amount');
        $totalDisbursed = $company->budgetTransactions()->where('status', 'approved')->sum('amount');
        $totalPending   = $company->budgetTransactions()->where('status', 'pending')->sum('amount');

        $recentPlacements = $placements->sortByDesc('start_date')->take(5);

        return view('company.dashboard', compact(
            'company',
            'activePlacements',
            'completedPlacements',
            'pendingFeedbackCount',
            'totalAllocated',
            'totalDisbursed',
            'totalPending',
            'recentPlacements'
        ));
    }
}
