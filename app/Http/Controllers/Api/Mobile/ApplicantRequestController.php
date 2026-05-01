<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\PublicPortalController;
use App\Models\ApplicantRequest;
use App\Models\SyarikatPelaksana;
use App\Models\Talent;
use Illuminate\Http\Request;

class ApplicantRequestController extends BaseMobileController
{
    public function implementationCompanyOptions(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->hasRole('rakan_kolaborasi')) {
            return $this->error('Forbidden.', 403);
        }

        return $this->success([
            'items' => SyarikatPelaksana::query()
                ->orderBy('nama_syarikat')
                ->get(['id_pelaksana', 'nama_syarikat'])
                ->map(fn (SyarikatPelaksana $company) => [
                    'id' => $company->id_pelaksana,
                    'company_name' => $company->nama_syarikat,
                ])
                ->values(),
        ]);
    }

    public function indexForCompany(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->hasRole('rakan_kolaborasi')) {
            return $this->error('Forbidden.', 403);
        }
        $placementCompany = $user?->syarikatPenempatan;

        if (!$placementCompany) {
            return $this->error('Placement company information is not linked to this account.', 422);
        }

        $query = ApplicantRequest::query()
            ->with(['talent', 'implementingCompany', 'placementCompany', 'requestedBy', 'reviewedBy'])
            ->where('placement_company_id', $placementCompany->id_syarikat)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return $this->success([
            'items' => $query->get()->map(fn (ApplicantRequest $item) => $this->payload($item))->values(),
        ]);
    }

    public function storeForCompany(Request $request)
    {
        $validated = $request->validate([
            'talent_id' => ['required', 'integer', 'exists:talents,id'],
            'implementing_company_id' => ['required', 'string', 'exists:syarikat_pelaksana,id_pelaksana'],
            'request_message' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();
        if (!$user || !$user->hasRole('rakan_kolaborasi')) {
            return $this->error('Forbidden.', 403);
        }
        $placementCompany = $user?->syarikatPenempatan;

        if (!$placementCompany) {
            return $this->error('Placement company information is not linked to this account.', 422);
        }

        $talent = Talent::findOrFail($validated['talent_id']);

        if (!$this->isAvailablePortalTalent($talent)) {
            return $this->error('Talent is not available in the portal.', 404);
        }

        $applicantRequest = ApplicantRequest::updateOrCreate(
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

        $applicantRequest->load(['talent', 'implementingCompany', 'placementCompany', 'requestedBy', 'reviewedBy']);

        return $this->success([
            'request' => $this->payload($applicantRequest),
        ], 'Applicant request sent successfully.');
    }

    public function indexForAdmin(Request $request)
    {
        $query = ApplicantRequest::query()
            ->with(['talent', 'implementingCompany', 'placementCompany', 'requestedBy', 'reviewedBy'])
            ->latest();

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
                })->orWhereHas('placementCompany', function ($companyQuery) use ($search) {
                    $companyQuery->where('nama_syarikat', 'like', "%{$search}%")
                        ->orWhere('id_syarikat', 'like', "%{$search}%")
                        ->orWhere('pic', 'like', "%{$search}%");
                })->orWhereHas('implementingCompany', function ($companyQuery) use ($search) {
                    $companyQuery->where('nama_syarikat', 'like', "%{$search}%")
                        ->orWhere('id_pelaksana', 'like', "%{$search}%")
                        ->orWhere('pic_syarikat', 'like', "%{$search}%");
                });
            });
        }

        return $this->success([
            'summary' => [
                'pending_implementation_review' => ApplicantRequest::where('status', ApplicantRequest::STATUS_PENDING_IMPLEMENTATION_REVIEW)->count(),
                'rejected_by_implementation' => ApplicantRequest::where('status', ApplicantRequest::STATUS_REJECTED_BY_IMPLEMENTATION)->count(),
                'pending_admin_approval' => ApplicantRequest::where('status', ApplicantRequest::STATUS_PENDING_ADMIN_APPROVAL)->count(),
                'rejected_by_admin' => ApplicantRequest::where('status', ApplicantRequest::STATUS_REJECTED_BY_ADMIN)->count(),
                'approved' => ApplicantRequest::where('status', ApplicantRequest::STATUS_APPROVED)->count(),
            ],
            'items' => $query->get()->map(fn (ApplicantRequest $item) => $this->payload($item))->values(),
        ]);
    }

    public function indexForPelaksana(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->hasRole('syarikat_pelaksana') || !$user->id_pelaksana) {
            return $this->error('Forbidden.', 403);
        }

        $query = ApplicantRequest::query()
            ->with(['talent', 'implementingCompany', 'placementCompany', 'requestedBy', 'reviewedBy'])
            ->where('implementing_company_id', $user->id_pelaksana)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return $this->success([
            'items' => $query->get()->map(fn (ApplicantRequest $item) => $this->payload($item))->values(),
        ]);
    }

    public function acceptForPelaksana(Request $request, ApplicantRequest $applicantRequest)
    {
        $this->authorizePelaksanaRequest($request, $applicantRequest);

        if ($applicantRequest->status !== ApplicantRequest::STATUS_PENDING_IMPLEMENTATION_REVIEW) {
            return $this->error('Request is not pending implementation review.', 422);
        }

        $applicantRequest->update([
            'status' => ApplicantRequest::STATUS_PENDING_ADMIN_APPROVAL,
            'review_notes' => null,
            'reviewed_by_user_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $applicantRequest->load(['talent', 'implementingCompany', 'placementCompany', 'requestedBy', 'reviewedBy']);

        return $this->success([
            'request' => $this->payload($applicantRequest),
        ], 'Applicant request accepted and sent for Admin / PMO approval.');
    }

    public function rejectForPelaksana(Request $request, ApplicantRequest $applicantRequest)
    {
        $this->authorizePelaksanaRequest($request, $applicantRequest);

        if ($applicantRequest->status !== ApplicantRequest::STATUS_PENDING_IMPLEMENTATION_REVIEW) {
            return $this->error('Request is not pending implementation review.', 422);
        }

        $request->validate([
            'review_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $applicantRequest->update([
            'status' => ApplicantRequest::STATUS_REJECTED_BY_IMPLEMENTATION,
            'review_notes' => $request->input('review_notes'),
            'reviewed_by_user_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $applicantRequest->load(['talent', 'implementingCompany', 'placementCompany', 'requestedBy', 'reviewedBy']);

        return $this->success([
            'request' => $this->payload($applicantRequest),
        ], 'Applicant request rejected.');
    }

    public function approve(Request $request, ApplicantRequest $applicantRequest)
    {
        if (!$request->user()?->hasAnyRole(['super_admin', 'pmo_admin'])) {
            return $this->error('Forbidden.', 403);
        }

        if ($applicantRequest->status !== ApplicantRequest::STATUS_PENDING_ADMIN_APPROVAL) {
            return $this->error('Request is not pending Admin / PMO approval.', 422);
        }

        if (!$this->isAvailablePortalTalent($applicantRequest->talent)) {
            return $this->error('Talent is no longer available.', 422);
        }

        $applicantRequest->talent()->update([
            'id_pelaksana' => $applicantRequest->implementing_company_id,
            'id_syarikat_penempatan' => $applicantRequest->placement_company_id,
            'status' => 'assigned',
            'status_aktif' => 'Aktif',
        ]);

        $applicantRequest->update([
            'status' => ApplicantRequest::STATUS_APPROVED,
            'review_notes' => $request->input('review_notes'),
            'reviewed_by_user_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $applicantRequest->load(['talent', 'implementingCompany', 'placementCompany', 'requestedBy', 'reviewedBy']);

        return $this->success([
            'request' => $this->payload($applicantRequest),
        ], 'Applicant request approved successfully.');
    }

    public function reject(Request $request, ApplicantRequest $applicantRequest)
    {
        if (!$request->user()?->hasAnyRole(['super_admin', 'pmo_admin'])) {
            return $this->error('Forbidden.', 403);
        }

        $request->validate([
            'review_notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($applicantRequest->status !== ApplicantRequest::STATUS_PENDING_ADMIN_APPROVAL) {
            return $this->error('Request is not pending Admin / PMO approval.', 422);
        }

        $applicantRequest->update([
            'status' => ApplicantRequest::STATUS_REJECTED_BY_ADMIN,
            'review_notes' => $request->input('review_notes'),
            'reviewed_by_user_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $applicantRequest->load(['talent', 'implementingCompany', 'placementCompany', 'requestedBy', 'reviewedBy']);

        return $this->success([
            'request' => $this->payload($applicantRequest),
        ], 'Applicant request rejected successfully.');
    }

    private function payload(ApplicantRequest $item): array
    {
        return [
            'id' => $item->id,
            'status' => $item->status,
            'status_label' => $item->status_label,
            'request_message' => $item->request_message,
            'review_notes' => $item->review_notes,
            'requested_at' => optional($item->created_at)->toIso8601String(),
            'reviewed_at' => optional($item->reviewed_at)->toIso8601String(),
            'talent' => [
                'id' => $item->talent?->id,
                'graduate_id' => $item->talent?->id_graduan,
                'full_name' => $item->talent?->full_name,
                'email' => $item->talent?->email,
                'university' => $item->talent?->university,
                'programme' => $item->talent?->programme,
            ],
            'placement_company' => [
                'id' => $item->placementCompany?->id_syarikat,
                'company_name' => $item->placementCompany?->nama_syarikat,
                'pic_name' => $item->placementCompany?->pic,
                'pic_email' => $item->placementCompany?->email_pic,
            ],
            'implementing_company' => [
                'id' => $item->implementingCompany?->id_pelaksana,
                'company_name' => $item->implementingCompany?->nama_syarikat,
                'pic_name' => $item->implementingCompany?->pic_syarikat,
                'pic_email' => $item->implementingCompany?->email_pic,
            ],
            'requested_by' => [
                'id' => $item->requestedBy?->id,
                'full_name' => $item->requestedBy?->full_name,
                'email' => $item->requestedBy?->email,
            ],
            'reviewed_by' => [
                'id' => $item->reviewedBy?->id,
                'full_name' => $item->reviewedBy?->full_name,
                'email' => $item->reviewedBy?->email,
            ],
        ];
    }

    private function authorizePelaksanaRequest(Request $request, ApplicantRequest $applicantRequest): void
    {
        $user = $request->user();
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
