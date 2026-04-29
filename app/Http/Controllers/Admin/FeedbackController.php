<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InternshipFeedback;
use App\Models\Placement;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = InternshipFeedback::with(['placement.talent', 'placement.company']);

        if ($request->filled('feedback_from')) {
            $query->where('feedback_from', $request->feedback_from);
        }

        if ($request->filled('placement_id')) {
            $query->where('placement_id', $request->placement_id);
        }

        $feedbacks = $query->orderByDesc('submitted_at')->paginate(20)->withQueryString();

        $placements = Placement::with(['talent', 'company'])
            ->whereIn('placement_status', ['active', 'completed'])
            ->get();

        return view('admin.feedback.index', [
            'feedbacks' => $feedbacks,
            'placements' => $placements,
            'showGenericCreateButton' => $this->showGenericCreateButton($request),
        ]);
    }

    public function create(Request $request)
    {
        $feedbackSourceContext = $this->feedbackSourceContext($request);
        $placements = $this->placementsQueryForUser($request)
            ->get();

        $selectedPlacement = $this->resolveSelectedPlacement($request, $placements);

        if ($this->isTalentUser($request) && !$selectedPlacement) {
            return redirect()
                ->route('admin.placements.index')
                ->with('error', __('messages.select_placement_from_placement_page'));
        }

        $placementLocked = $this->isTalentUser($request) && (bool) $selectedPlacement;

        return view('admin.feedback.create', [
            'placements' => $placements,
            'selectedPlacement' => $selectedPlacement,
            'placementLocked' => $placementLocked,
            'feedbackSourceOptions' => $feedbackSourceContext['options'],
            'defaultFeedbackSource' => $feedbackSourceContext['default'],
            'feedbackSourceLocked' => $feedbackSourceContext['locked'],
        ]);
    }

    public function store(Request $request)
    {
        $feedbackSourceContext = $this->feedbackSourceContext($request);
        $placements = $this->placementsQueryForUser($request)->get();
        $selectedPlacement = $this->resolveSelectedPlacement($request, $placements);

        if ($this->isTalentUser($request) && $selectedPlacement) {
            $request->merge(['placement_id' => $selectedPlacement->id]);
        }

        $validated = $request->validate([
            'placement_id' => [
                'required',
                Rule::exists('placements', 'id')->where(function ($query) use ($request) {
                    $this->applyPlacementScope($query, $request);
                }),
            ],
            'feedback_from' => ['required', Rule::in(array_keys($feedbackSourceContext['options']))],
            'score_technical' => 'nullable|integer|min:1|max:5',
            'score_communication' => 'nullable|integer|min:1|max:5',
            'score_discipline' => 'nullable|integer|min:1|max:5',
            'score_problem_solving' => 'nullable|integer|min:1|max:5',
            'score_professionalism' => 'nullable|integer|min:1|max:5',
            'comments' => 'nullable|string',
        ]);

        if ($feedbackSourceContext['locked']) {
            $validated['feedback_from'] = $feedbackSourceContext['default'];
        }

        $validated['submitted_at'] = now();

        $feedback = InternshipFeedback::create($validated);

        AuditLog::log('feedback', 'create', $feedback->id, null, $feedback->toArray());

        return redirect()->route('admin.feedback.index')
            ->with('success', __('messages.feedback_submitted'));
    }

    public function show(InternshipFeedback $feedback)
    {
        $feedback->load(['placement.talent', 'placement.company']);

        return view('admin.feedback.show', compact('feedback'));
    }

    private function placementsQueryForUser(Request $request)
    {
        $query = Placement::with(['talent', 'company'])
            ->whereIn('placement_status', ['active', 'completed'])
            ->orderByRaw("CASE WHEN placement_status = 'active' THEN 0 ELSE 1 END")
            ->orderByDesc('start_date')
            ->orderByDesc('id');

        $this->applyPlacementScope($query, $request);

        return $query;
    }

    private function applyPlacementScope($query, Request $request): void
    {
        $user = $request->user();
        $roleName = $user?->role?->role_name;

        if ($roleName === 'rakan_kolaborasi') {
            $query->whereHas('placement', fn($q) => $q->where('id_syarikat_penempatan', $user?->id_syarikat_penempatan));
            return;
        }

        if ($roleName === 'talent') {
            $query->where('talent_id', $user?->talent_id ?? 0);
        }
    }

    private function feedbackSourceContext(Request $request): array
    {
        $roleName = $request->user()?->role?->role_name;

        return match ($roleName) {
            'rakan_kolaborasi' => [
                'options' => ['company' => __('common.feedback_from_company')],
                'default' => 'company',
                'locked' => true,
            ],
            'talent' => [
                'options' => ['talent' => __('common.feedback_from_talent')],
                'default' => 'talent',
                'locked' => true,
            ],
            'super_admin', 'pmo_admin' => [
                'options' => ['yltat' => __('common.feedback_from_yltat')],
                'default' => 'yltat',
                'locked' => true,
            ],
            default => [
                'options' => [
                    'company' => __('common.feedback_from_company'),
                    'talent' => __('common.feedback_from_talent'),
                    'yltat' => __('common.feedback_from_yltat'),
                ],
                'default' => null,
                'locked' => false,
            ],
        };
    }

    private function showGenericCreateButton(Request $request): bool
    {
        return true;
    }

    private function resolveSelectedPlacement(Request $request, $placements): ?Placement
    {
        if ($request->filled('placement_id')) {
            return $placements->firstWhere('id', (int) $request->placement_id);
        }

        if ($this->isTalentUser($request)) {
            return $placements->first();
        }

        return null;
    }

    private function isTalentUser(Request $request): bool
    {
        return $request->user()?->role?->role_name === 'talent';
    }
}
