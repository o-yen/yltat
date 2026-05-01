<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PublicPortalController;
use App\Models\ApplicantRequest;
use App\Models\Talent;
use Illuminate\Http\Request;

class ApplicantRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ApplicantRequest::query()
            ->with(['talent', 'implementingCompany', 'placementCompany', 'requestedBy', 'reviewedBy'])
            ->latest();

        $user = $request->user();
        $role = $user?->role?->role_name;

        if ($role === 'syarikat_pelaksana') {
            abort_unless($user->id_pelaksana, 403);
            $query->where('implementing_company_id', $user->id_pelaksana);
        } elseif ($role === 'rakan_kolaborasi') {
            abort_unless($user->id_syarikat_penempatan, 403);
            $query->where('placement_company_id', $user->id_syarikat_penempatan);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->whereHas('talent', function ($talentQuery) use ($search) {
                    $talentQuery->where('full_name', 'like', "%{$search}%")
                        ->orWhere('id_graduan', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('implementingCompany', function ($companyQuery) use ($search) {
                    $companyQuery->where('nama_syarikat', 'like', "%{$search}%")
                        ->orWhere('id_pelaksana', 'like', "%{$search}%")
                        ->orWhere('pic_syarikat', 'like', "%{$search}%");
                })->orWhereHas('placementCompany', function ($companyQuery) use ($search) {
                    $companyQuery->where('nama_syarikat', 'like', "%{$search}%")
                        ->orWhere('id_syarikat', 'like', "%{$search}%")
                        ->orWhere('pic', 'like', "%{$search}%");
                });
            });
        }

        $requests = $query->paginate(20)->withQueryString();
        $counts = collect(ApplicantRequest::statusLabels())
            ->map(fn ($label, $status) => $this->visibleRequests($user)->where('status', $status)->count())
            ->all();

        $statusLabels = ApplicantRequest::statusLabels();
        $canAdminReview = $user?->hasAnyRole(['super_admin', 'pmo_admin']);
        $canImplementationReview = $user?->hasRole('syarikat_pelaksana');

        return view('admin.applicant-requests.index', compact('requests', 'counts', 'statusLabels', 'canAdminReview', 'canImplementationReview'));
    }

    public function storeFromPortal(Request $request, Talent $talent)
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('rakan_kolaborasi'), 403);

        if (!$this->isAvailablePortalTalent($talent)) {
            abort(404);
        }

        $validated = $request->validate([
            'implementing_company_id' => ['required', 'string', 'exists:syarikat_pelaksana,id_pelaksana'],
            'request_message' => ['nullable', 'string', 'max:500'],
        ]);

        $user = auth()->user();
        $placementCompany = $user->syarikatPenempatan;

        if (!$placementCompany) {
            return back()->with('error', 'Placement company information is not linked to your account.');
        }

        ApplicantRequest::updateOrCreate(
            [
                'talent_id' => $talent->id,
                'placement_company_id' => $placementCompany->id_syarikat,
            ],
            [
                'implementing_company_id' => $validated['implementing_company_id'],
                'requested_by_user_id' => $user->id,
                'status' => ApplicantRequest::STATUS_PENDING_IMPLEMENTATION_REVIEW,
                'request_message' => $validated['request_message'] ?? null,
                'review_notes' => null,
                'reviewed_by_user_id' => null,
                'reviewed_at' => null,
            ]
        );

        return back()->with('success', 'Applicant request sent to the selected implementation company successfully.');
    }

    public function acceptImplementation(ApplicantRequest $applicantRequest)
    {
        $this->authorizeImplementationReview($applicantRequest);

        abort_unless($applicantRequest->status === ApplicantRequest::STATUS_PENDING_IMPLEMENTATION_REVIEW, 403);

        $applicantRequest->update([
            'status' => ApplicantRequest::STATUS_PENDING_ADMIN_APPROVAL,
            'review_notes' => null,
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Applicant request accepted and sent to Admin / PMO for final approval.');
    }

    public function rejectImplementation(Request $request, ApplicantRequest $applicantRequest)
    {
        $this->authorizeImplementationReview($applicantRequest);

        abort_unless($applicantRequest->status === ApplicantRequest::STATUS_PENDING_IMPLEMENTATION_REVIEW, 403);

        $request->validate([
            'review_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $applicantRequest->update([
            'status' => ApplicantRequest::STATUS_REJECTED_BY_IMPLEMENTATION,
            'review_notes' => $request->input('review_notes'),
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Applicant request rejected.');
    }

    public function approve(ApplicantRequest $applicantRequest)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'pmo_admin']), 403);

        if ($applicantRequest->status === ApplicantRequest::STATUS_APPROVED) {
            return back()->with('success', 'Applicant request is already approved.');
        }

        abort_unless($applicantRequest->status === ApplicantRequest::STATUS_PENDING_ADMIN_APPROVAL, 403);

        if (!$applicantRequest->implementing_company_id || !$applicantRequest->placement_company_id) {
            return back()->with('error', 'This request is missing company information and cannot be approved.');
        }

        if (!$this->isAvailablePortalTalent($applicantRequest->talent)) {
            return back()->with('error', 'This talent is no longer available for assignment.');
        }

        $applicantRequest->talent()->update([
            'id_pelaksana' => $applicantRequest->implementing_company_id,
            'id_syarikat_penempatan' => $applicantRequest->placement_company_id,
            'status' => 'assigned',
            'status_aktif' => 'Aktif',
        ]);

        $applicantRequest->update([
            'status' => ApplicantRequest::STATUS_APPROVED,
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Applicant request approved successfully.');
    }

    public function reject(Request $request, ApplicantRequest $applicantRequest)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'pmo_admin']), 403);

        $request->validate([
            'review_notes' => ['nullable', 'string', 'max:500'],
        ]);

        abort_unless($applicantRequest->status === ApplicantRequest::STATUS_PENDING_ADMIN_APPROVAL, 403);

        $applicantRequest->update([
            'status' => ApplicantRequest::STATUS_REJECTED_BY_ADMIN,
            'review_notes' => $request->input('review_notes'),
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Applicant request rejected successfully.');
    }

    private function visibleRequests($user)
    {
        $query = ApplicantRequest::query();

        if ($user?->hasRole('syarikat_pelaksana')) {
            return $query->where('implementing_company_id', $user->id_pelaksana);
        }

        if ($user?->hasRole('rakan_kolaborasi')) {
            return $query->where('placement_company_id', $user->id_syarikat_penempatan);
        }

        return $query;
    }

    private function authorizeImplementationReview(ApplicantRequest $applicantRequest): void
    {
        $user = auth()->user();

        abort_unless($user?->hasRole('syarikat_pelaksana'), 403);
        abort_unless($user->id_pelaksana && $applicantRequest->implementing_company_id === $user->id_pelaksana, 403);
    }

    private function isAvailablePortalTalent(?Talent $talent): bool
    {
        if (!$talent) {
            return false;
        }

        return !empty($talent->id_graduan)
            && (bool) $talent->public_visibility
            && empty($talent->id_pelaksana)
            && empty($talent->id_syarikat_penempatan)
            && in_array($talent->status_aktif ?: $talent->status, PublicPortalController::PUBLIC_TALENT_STATUSES, true);
    }
}
