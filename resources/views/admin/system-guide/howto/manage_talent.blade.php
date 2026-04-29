@extends('admin.system-guide.howto._layout')

@section('howto-content')

{{-- Section 1: Add Graduate --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">1</span>
        {{ __('howto.manage_talent_step1_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.manage_talent_step1_desc') }}</p>
    </div>
</section>

{{-- Section 2: Edit --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">2</span>
        {{ __('howto.manage_talent_step2_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.manage_talent_step2_desc') }}</p>
    </div>
</section>

{{-- Section 3: Assign Company --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">3</span>
        {{ __('howto.manage_talent_step3_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.manage_talent_step3_desc') }}</p>
    </div>
</section>

{{-- Section 4: Search & Filter --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">4</span>
        {{ __('howto.manage_talent_step4_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.manage_talent_step4_desc') }}</p>
    </div>
</section>

{{-- Info --}}
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
    <p class="text-blue-800">{{ __('howto.manage_talent_info') }}</p>
</div>

@endsection
