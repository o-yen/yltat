@extends('admin.system-guide.modules._layout')

@section('guide-icon')
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
@endsection

@section('guide-content')
{{-- 1. Overview --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">1</span>
        {{ __('guide.section_overview') }}
    </h2>
    <p>{{ __('guide.kewangan_overview') }}</p>
</section>

{{-- 2. Who Can Access --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">2</span>
        {{ __('guide.section_access') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('guide.kewangan_access') }}</p>
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
                <span>{{ __("guide.kewangan_feat{$i}") }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- 4. Payment Status --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">4</span>
        {{ __('guide.kewangan_statuses_title') }}
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
                    ['Selesai', 'guide.kewangan_status_selesai'],
                    ['Dalam Proses', 'guide.kewangan_status_proses'],
                    ['Lewat', 'guide.kewangan_status_lewat'],
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

{{-- 5. Tips --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">5</span>
        {{ __('guide.section_tips') }}
    </h2>
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 space-y-2">
        @foreach(range(1, 2) as $i)
            <p class="flex items-start gap-2"><span class="text-amber-500 font-bold mt-0.5">!</span>{{ __("guide.kewangan_tip{$i}") }}</p>
        @endforeach
    </div>
</section>
@endsection
