<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $counts = [
            'pending' => $this->visibleRequests($user)->where('status', 'pending')->count(),
            'approved' => $this->visibleRequests($user)->where('status', 'approved')->count(),
            'rejected' => $this->visibleRequests($user)->where('status', 'rejected')->count(),
        ];

        $canReview = $user?->hasAnyRole(['super_admin', 'pmo_admin']);

        return view('admin.applicant-requests.index', compact('requests', 'counts', 'canReview'));
    }

    public function storeFromPortal(Request $request, Talent $talent)
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('syarikat_pelaksana'), 403);

        if (!$talent->public_visibility) {
            abort(404);
        }

        $user = auth()->user();
        $implementingCompany = $user->syarikatPelaksana;

        if (!$implementingCompany) {
            return back()->with('error', 'Implementing company information is not linked to your account.');
        }

        ApplicantRequest::updateOrCreate(
            [
                'talent_id' => $talent->id,
                'implementing_company_id' => $implementingCompany->id_pelaksana,
            ],
            [
                'requested_by_user_id' => $user->id,
                'placement_company_id' => null,
                'status' => 'pending',
                'request_message' => $request->input('request_message'),
                'review_notes' => null,
                'reviewed_by_user_id' => null,
                'reviewed_at' => null,
            ]
        );

        return back()->with('success', 'Applicant request sent to Admin successfully.');
    }

    public function approve(ApplicantRequest $applicantRequest)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'pmo_admin']), 403);

        if ($applicantRequest->status === 'approved') {
            return back()->with('success', 'Applicant request is already approved.');
        }

        if ($applicantRequest->implementing_company_id) {
            $applicantRequest->talent()->update([
                'id_pelaksana' => $applicantRequest->implementing_company_id,
                'status' => 'approved',
                'status_aktif' => 'Aktif',
            ]);
        } elseif ($applicantRequest->placement_company_id) {
            $applicantRequest->talent()->update([
                'id_syarikat_penempatan' => $applicantRequest->placement_company_id,
                'status' => 'assigned',
                'status_aktif' => 'Aktif',
            ]);
        } else {
            return back()->with('error', 'This request is missing company information and cannot be approved.');
        }

        $applicantRequest->update([
            'status' => 'approved',
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

        $applicantRequest->update([
            'status' => 'rejected',
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

        return $query;
    }
}
