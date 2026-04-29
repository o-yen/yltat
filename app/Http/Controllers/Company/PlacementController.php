<?php

namespace App\Http\Controllers\Company;

use App\Models\Placement;
use Illuminate\Http\Request;

class PlacementController extends BaseCompanyController
{

    public function index(Request $request)
    {
        $company = $this->getCompany();

        $query = $company->placements()->with(['talent', 'batch', 'feedback']);

        if ($request->filled('status')) {
            $query->where('placement_status', $request->status);
        }

        $placements = $query->orderByRaw("CASE placement_status
            WHEN 'active' THEN 1
            WHEN 'confirmed' THEN 2
            WHEN 'planned' THEN 3
            WHEN 'completed' THEN 4
            ELSE 5 END")
            ->orderByDesc('start_date')
            ->paginate(15)
            ->withQueryString();

        return view('company.placements.index', compact('company', 'placements'));
    }

    public function show(Placement $placement)
    {
        $company = $this->getCompany();

        if ((int) $placement->company_id !== (int) $company->id) {
            return redirect()
                ->route('company.placements.index')
                ->with('error', __('messages.company_access_denied'));
        }

        $placement->load(['talent.documents', 'batch', 'feedback']);

        $transactions = $placement->budgetTransactions()->orderByDesc('transaction_date')->get();

        $totalDisbursed = $transactions->where('status', 'approved')->sum('amount');
        $totalPending   = $transactions->where('status', 'pending')->sum('amount');

        $companyFeedback = $placement->feedback()->where('feedback_from', 'company')->first();
        $allFeedback     = $placement->feedback()->get();

        return view('company.placements.show', compact(
            'placement',
            'company',
            'transactions',
            'totalDisbursed',
            'totalPending',
            'companyFeedback',
            'allFeedback'
        ));
    }
}
