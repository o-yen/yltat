@extends('admin.system-guide.howto._layout')

@section('howto-content')

{{-- Section 1: Add Company --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">1</span>
        {{ __('howto.manage_pelaksana_step1_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.manage_pelaksana_step1_desc') }}</p>
    </div>
</section>

{{-- Section 2: Monitor Funding --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">2</span>
        {{ __('howto.manage_pelaksana_step2_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.manage_pelaksana_step2_desc') }}</p>
    </div>
</section>

{{-- Section 3: Track Compliance --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">3</span>
        {{ __('howto.manage_pelaksana_step3_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.manage_pelaksana_step3_desc') }}</p>
    </div>
</section>

{{-- Warning --}}
<div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
    <p class="flex items-start gap-2"><span class="text-amber-500 font-bold">!</span> {{ __('howto.manage_pelaksana_warn') }}</p>
</div>

@endsection
