@extends('admin.system-guide.modules._layout')

@section('guide-icon')
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
@endsection

@section('guide-content')
{{-- 1. Overview --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">1</span>
        {{ __('guide.section_overview') }}
    </h2>
    <p>{{ __('guide.logbook_overview') }}</p>
</section>

{{-- 2. Who Can Access --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">2</span>
        {{ __('guide.section_access') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('guide.logbook_access') }}</p>
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
                <span>{{ __("guide.logbook_feat{$i}") }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- 4. Submission Status --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">4</span>
        {{ __('guide.logbook_submission_status_title') }}
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
                    ['Dikemukakan', 'guide.logbook_status_dikemukakan'],
                    ['Dalam Semakan', 'guide.logbook_status_semakan'],
                    ['Lewat', 'guide.logbook_status_lewat'],
                    ['Belum Dikemukakan', 'guide.logbook_status_belum'],
                ] as [$status, $descKey])
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $status }}</td>
                        <td class="px-4 py-2">{{ __($descKey) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

{{-- 5. Review Status --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">5</span>
        {{ __('guide.logbook_review_status_title') }}
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
                    ['Lulus', 'guide.logbook_review_lulus'],
                    ['Dalam Proses', 'guide.logbook_review_proses'],
                    ['Perlu Semakan Semula', 'guide.logbook_review_semula'],
                    ['Belum Disemak', 'guide.logbook_review_belum'],
                ] as [$status, $descKey])
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $status }}</td>
                        <td class="px-4 py-2">{{ __($descKey) }}</td>
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
            <p class="flex items-start gap-2"><span class="text-amber-500 font-bold mt-0.5">!</span>{{ __("guide.logbook_tip{$i}") }}</p>
        @endforeach
    </div>
</section>
@endsection
