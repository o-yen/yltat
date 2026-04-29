<?php

namespace App\Http\Controllers\Company;

use App\Models\InternshipFeedback;
use App\Models\Placement;
use Illuminate\Http\Request;

class FeedbackController extends BaseCompanyController
{

    public function index()
    {
        $company = $this->getCompany();

        $feedbacks = InternshipFeedback::whereHas('placement', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->where('feedback_from', 'company')
            ->with(['placement.talent'])
            ->orderByDesc('submitted_at')
            ->paginate(15);

        $pendingPlacements = $company->placements()
            ->whereIn('placement_status', ['active', 'confirmed'])
            ->whereDoesntHave('feedback', function ($q) {
                $q->where('feedback_from', 'company');
            })
            ->with('talent')
            ->get();

        return view('company.feedback.index', compact('feedbacks', 'pendingPlacements', 'company'));
    }

    public function create(Request $request)
    {
        $company = $this->getCompany();

        $placements = $company->placements()
            ->whereIn('placement_status', ['active', 'confirmed'])
            ->whereDoesntHave('feedback', function ($q) {
                $q->where('feedback_from', 'company');
            })
            ->with('talent')
            ->get();

        $selectedPlacement = null;
        if ($request->filled('placement_id')) {
            $selectedPlacement = $placements->firstWhere('id', (int) $request->placement_id);
        }

        return view('company.feedback.create', compact('placements', 'selectedPlacement', 'company'));
    }

    public function store(Request $request)
    {
        $company = $this->getCompany();

        $request->validate([
            'placement_id'          => 'required|integer',
            'score_technical'       => 'required|integer|min:1|max:5',
            'score_communication'   => 'required|integer|min:1|max:5',
            'score_discipline'      => 'required|integer|min:1|max:5',
            'score_problem_solving' => 'required|integer|min:1|max:5',
            'score_professionalism' => 'required|integer|min:1|max:5',
            'comments'              => 'nullable|string|max:2000',
        ]);

        // Verify the placement belongs to this company
        $placement = Placement::where('id', $request->placement_id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        // Prevent duplicate company feedback
        if ($placement->feedback()->where('feedback_from', 'company')->exists()) {
            return back()->with('error', __('messages.company_feedback_already_submitted'));
        }

        InternshipFeedback::create([
            'placement_id'          => $placement->id,
            'feedback_from'         => 'company',
            'score_technical'       => $request->score_technical,
            'score_communication'   => $request->score_communication,
            'score_discipline'      => $request->score_discipline,
            'score_problem_solving' => $request->score_problem_solving,
            'score_professionalism' => $request->score_professionalism,
            'comments'              => $request->comments,
            'submitted_at'          => now(),
        ]);

        return redirect()->route('company.feedback.index')
            ->with('success', __('messages.feedback_submitted'));
    }

    public function show(InternshipFeedback $feedback)
    {
        $company = $this->getCompany();

        if ((int) $feedback->placement->company_id !== (int) $company->id) {
            return redirect()
                ->route('company.feedback.index')
                ->with('error', __('messages.company_access_denied'));
        }

        $feedback->load(['placement.talent']);

        return view('company.feedback.show', compact('feedback', 'company'));
    }
}
