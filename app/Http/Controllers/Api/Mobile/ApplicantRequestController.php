<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\ApplicantRequest;
use App\Models\Talent;
use Illuminate\Http\Request;

class ApplicantRequestController extends BaseMobileController
{
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
            ->with(['talent', 'placementCompany', 'requestedBy', 'reviewedBy'])
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

        if (!$talent->public_visibility) {
            return $this->error('Talent is not available in the portal.', 404);
        }

        $applicantRequest = ApplicantRequest::updateOrCreate(
            [
                'talent_id' => $talent->id,
                'placement_company_id' => $placementCompany->id_syarikat,
            ],
            [
                'requested_by_user_id' => $user->id,
                'status' => 'pending',
                'request_message' => $validated['request_message'] ?? null,
                'review_notes' => null,
                'reviewed_by_user_id' => null,
                'reviewed_at' => null,
            ]
        );

        $applicantRequest->load(['talent', 'placementCompany', 'requestedBy', 'reviewedBy']);

        return $this->success([
            'request' => $this->payload($applicantRequest),
        ], 'Applicant request sent successfully.');
    }

    public function indexForAdmin(Request $request)
    {
        $query = ApplicantRequest::query()
            ->with(['talent', 'placementCompany', 'requestedBy', 'reviewedBy'])
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
                });
            });
        }

        return $this->success([
            'summary' => [
                'pending' => ApplicantRequest::where('status', 'pending')->count(),
                'approved' => ApplicantRequest::where('status', 'approved')->count(),
                'rejected' => ApplicantRequest::where('status', 'rejected')->count(),
            ],
            'items' => $query->get()->map(fn (ApplicantRequest $item) => $this->payload($item))->values(),
        ]);
    }

    public function approve(Request $request, ApplicantRequest $applicantRequest)
    {
        if ($applicantRequest->status !== 'approved') {
            $applicantRequest->talent()->update([
                'id_syarikat_penempatan' => $applicantRequest->placement_company_id,
                'status' => 'assigned',
                'status_aktif' => 'Aktif',
            ]);
        }

        $applicantRequest->update([
            'status' => 'approved',
            'review_notes' => $request->input('review_notes'),
            'reviewed_by_user_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $applicantRequest->load(['talent', 'placementCompany', 'requestedBy', 'reviewedBy']);

        return $this->success([
            'request' => $this->payload($applicantRequest),
        ], 'Applicant request approved successfully.');
    }

    public function reject(Request $request, ApplicantRequest $applicantRequest)
    {
        $request->validate([
            'review_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $applicantRequest->update([
            'status' => 'rejected',
            'review_notes' => $request->input('review_notes'),
            'reviewed_by_user_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $applicantRequest->load(['talent', 'placementCompany', 'requestedBy', 'reviewedBy']);

        return $this->success([
            'request' => $this->payload($applicantRequest),
        ], 'Applicant request rejected successfully.');
    }

    private function payload(ApplicantRequest $item): array
    {
        return [
            'id' => $item->id,
            'status' => $item->status,
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
}
