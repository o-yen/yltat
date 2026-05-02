@extends('admin.system-guide.modules._layout')

@section('guide-icon')
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5m-1 7l-6-6V5a2 2 0 012-2h8a2 2 0 012 2v10l-6 6z"/>
@endsection

@section('guide-content')
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">1</span>
        {{ __('guide.section_overview') }}
    </h2>
    <p>{{ __('guide.applicant_requests_overview') }}</p>
</section>

<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">2</span>
        {{ __('guide.section_access') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('guide.applicant_requests_access') }}</p>
    </div>
</section>

<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">3</span>
        {{ __('guide.section_features') }}
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach(range(1, 4) as $i)
            <div class="flex items-start gap-2 bg-blue-50 rounded-lg p-3">
                <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ __("guide.applicant_requests_feat{$i}") }}</span>
            </div>
        @endforeach
    </div>
</section>

<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">4</span>
        {{ __('guide.applicant_requests_flow_title') }}
    </h2>
    <div class="space-y-3">
        @foreach([
            'search',
            'request',
            'implementation',
            'admin',
            'approved',
        ] as $index => $step)
            <div class="flex items-start gap-3 bg-gray-50 rounded-lg p-3">
                <span class="w-6 h-6 rounded-full bg-[#1E3A5F] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">{{ $index + 1 }}</span>
                <span>{{ __("guide.applicant_requests_flow_{$step}") }}</span>
            </div>
        @endforeach
    </div>
</section>

<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">5</span>
        {{ __('guide.applicant_requests_status_title') }}
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('guide.th_status') }}</th>
                    <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('guide.th_meaning') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach([
                    ['pending_implementation_review', 'guide.applicant_requests_status_pending_implementation'],
                    ['rejected_by_implementation', 'guide.applicant_requests_status_rejected_implementation'],
                    ['pending_admin_approval', 'guide.applicant_requests_status_pending_admin'],
                    ['rejected_by_admin', 'guide.applicant_requests_status_rejected_admin'],
                    ['approved', 'guide.applicant_requests_status_approved'],
                ] as [$status, $descKey])
                    <tr>
                        <td class="px-4 py-2 font-mono text-xs">{{ $status }}</td>
                        <td class="px-4 py-2">{{ __($descKey) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">6</span>
        {{ __('guide.section_tips') }}
    </h2>
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 space-y-2">
        @foreach(range(1, 2) as $i)
            <p class="flex items-start gap-2"><span class="text-amber-500 font-bold mt-0.5">!</span>{{ __("guide.applicant_requests_tip{$i}") }}</p>
        @endforeach
    </div>
</section>
@endsection
