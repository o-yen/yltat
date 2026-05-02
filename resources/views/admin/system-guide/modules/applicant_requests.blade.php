@extends('admin.system-guide.modules._layout')

@section('guide-icon')
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5m-1 7l-6-6V5a2 2 0 012-2h8a2 2 0 012 2v10l-6 6z"/>
@endsection

@section('guide-content')
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">1</span>
        Overview
    </h2>
    <p>
        The Applicant Request module lets placement companies express interest in available talents from the Talent Search Portal.
        Requests do not assign a talent immediately. Each request must first be reviewed by the relevant Implementation Company,
        then approved or rejected by Super Admin / PMO before the talent enters the placement workflow.
    </p>
</section>

<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">2</span>
        Who Can Access
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>
            Available to Super Admin, PMO Admin, MINDEF Viewer, Implementation Company, and Placement Company accounts.
            Placement Companies can create and track their own requests. Implementation Companies can accept or reject requests
            linked to their company. Super Admin / PMO has final approval authority. MINDEF Viewer has read-only access only.
        </p>
    </div>
</section>

<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">3</span>
        Key Features
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach([
            'Placement Companies can submit interest only for approved, public-visible talents that are not assigned yet.',
            'Implementation Companies can review requests that are relevant to their own company.',
            'Super Admin / PMO can approve or reject requests after Implementation Company acceptance.',
            'Approved requests assign the talent to the correct Implementation Company and Placement Company.',
        ] as $feature)
            <div class="flex items-start gap-2 bg-blue-50 rounded-lg p-3">
                <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ $feature }}</span>
            </div>
        @endforeach
    </div>
</section>

<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">4</span>
        Request Flow
    </h2>
    <div class="space-y-3">
        @foreach([
            'Placement Company searches the Talent Search Portal for available, unassigned talents.',
            'Placement Company submits an interest request and selects the relevant Implementation Company.',
            'Implementation Company reviews the request and either accepts or rejects it.',
            'If accepted, the request moves to Super Admin / PMO for final approval.',
            'If approved by Super Admin / PMO, the talent appears in both companies Manage Placement pages.',
        ] as $index => $step)
            <div class="flex items-start gap-3 bg-gray-50 rounded-lg p-3">
                <span class="w-6 h-6 rounded-full bg-[#1E3A5F] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">{{ $index + 1 }}</span>
                <span>{{ $step }}</span>
            </div>
        @endforeach
    </div>
</section>

<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">5</span>
        Request Statuses
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-2 font-semibold text-gray-600">Status</th>
                    <th class="text-left px-4 py-2 font-semibold text-gray-600">Meaning</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach([
                    ['pending_implementation_review', 'Waiting for Implementation Company review.'],
                    ['rejected_by_implementation', 'Rejected by Implementation Company. The talent remains available unless assigned through another flow.'],
                    ['pending_admin_approval', 'Accepted by Implementation Company and waiting for Super Admin / PMO final approval.'],
                    ['rejected_by_admin', 'Rejected by Super Admin / PMO. The talent remains available unless assigned through another flow.'],
                    ['approved', 'Approved and assigned into the placement workflow.'],
                ] as [$status, $description])
                    <tr>
                        <td class="px-4 py-2 font-mono text-xs">{{ $status }}</td>
                        <td class="px-4 py-2">{{ $description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">6</span>
        Role Rules
    </h2>
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 space-y-2">
        @foreach([
            'Implementation Companies cannot request or select talents directly from the Talent Portal.',
            'Placement Companies cannot directly assign talents. They can only submit interest requests.',
            'Pending or rejected requests do not assign a talent.',
            'Talent assignment happens only after Super Admin / PMO final approval.',
            'Approved talents no longer appear in the available Talent Search Portal.',
        ] as $rule)
            <p class="flex items-start gap-2"><span class="text-amber-500 font-bold mt-0.5">!</span>{{ $rule }}</p>
        @endforeach
    </div>
</section>
@endsection
