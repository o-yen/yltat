@extends('admin.system-guide.modules._layout')

@section('guide-icon')
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
@endsection

@section('guide-content')
{{-- 1. Overview --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">1</span>
        {{ __('guide.section_overview') }}
    </h2>
    <p>{{ __('guide.feedback_overview') }}</p>
</section>

{{-- 2. Who Can Access --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">2</span>
        {{ __('guide.section_access') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('guide.feedback_access') }}</p>
    </div>
</section>

{{-- 3. Key Features --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">3</span>
        {{ __('guide.section_features') }}
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach(range(1, 4) as $i)
            <div class="flex items-start gap-2 bg-blue-50 rounded-lg p-3">
                <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ __("guide.feedback_feat{$i}") }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- 4. Score Dimensions --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">4</span>
        {{ __('guide.feedback_scores_title') }}
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('guide.th_dimension') }}</th>
                    <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('guide.th_description') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach([
                    ['guide.feedback_dim_technical', 'guide.feedback_dim_technical_desc'],
                    ['guide.feedback_dim_communication', 'guide.feedback_dim_communication_desc'],
                    ['guide.feedback_dim_discipline', 'guide.feedback_dim_discipline_desc'],
                    ['guide.feedback_dim_problem_solving', 'guide.feedback_dim_problem_solving_desc'],
                    ['guide.feedback_dim_professionalism', 'guide.feedback_dim_professionalism_desc'],
                ] as [$name, $desc])
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ __($name) }}</td>
                        <td class="px-4 py-2">{{ __($desc) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

{{-- 5. Feedback Sources --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">5</span>
        {{ __('guide.feedback_sources_title') }}
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('guide.th_source') }}</th>
                    <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('guide.th_description') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach([
                    ['guide.feedback_src_company', 'guide.feedback_src_company_desc'],
                    ['guide.feedback_src_talent', 'guide.feedback_src_talent_desc'],
                    ['guide.feedback_src_yltat', 'guide.feedback_src_yltat_desc'],
                ] as [$name, $desc])
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ __($name) }}</td>
                        <td class="px-4 py-2">{{ __($desc) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

{{-- 6. Tips --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">6</span>
        {{ __('guide.section_tips') }}
    </h2>
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 space-y-2">
        @foreach(range(1, 2) as $i)
            <p class="flex items-start gap-2"><span class="text-amber-500 font-bold mt-0.5">!</span>{{ __("guide.feedback_tip{$i}") }}</p>
        @endforeach
    </div>
</section>
@endsection
