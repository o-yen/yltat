@extends('admin.system-guide.modules._layout')

@section('guide-icon')
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
@endsection

@section('guide-content')
{{-- 1. Overview --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">1</span>
        {{ __('guide.section_overview') }}
    </h2>
    <p>{{ __('guide.penempatan_overview') }}</p>
</section>

{{-- 2. Who Can Access --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">2</span>
        {{ __('guide.section_access') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('guide.penempatan_access') }}</p>
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
                <span>{{ __("guide.penempatan_feat{$i}") }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- 4. Tips --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">4</span>
        {{ __('guide.section_tips') }}
    </h2>
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 space-y-2">
        @foreach(range(1, 2) as $i)
            <p class="flex items-start gap-2"><span class="text-amber-500 font-bold mt-0.5">!</span>{{ __("guide.penempatan_tip{$i}") }}</p>
        @endforeach
    </div>
</section>
@endsection
