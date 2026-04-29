@extends('admin.system-guide.howto._layout')

@section('howto-content')

{{-- Section 1 --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">1</span>
        {{ __('howto.budget_allocation_step1_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.budget_allocation_step1_desc') }}</p>
    </div>
</section>

{{-- Section 2 --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">2</span>
        {{ __('howto.budget_allocation_step2_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.budget_allocation_step2_desc') }}</p>
    </div>
</section>

{{-- Section 3 --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">3</span>
        {{ __('howto.budget_allocation_step3_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.budget_allocation_step3_desc') }}</p>
    </div>
</section>

{{-- Section 4: Overrun Detection --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">4</span>
        {{ __('howto.budget_allocation_step4_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.budget_allocation_step4_desc') }}</p>
    </div>
</section>

{{-- Formula Box --}}
<div class="bg-gray-800 rounded-lg p-4 font-mono text-sm text-green-400 space-y-1">
    <p>{{ __('howto.budget_allocation_formula1') }}</p>
    <p>{{ __('howto.budget_allocation_formula2') }}</p>
    <p>{{ __('howto.budget_allocation_formula3') }}</p>
</div>

{{-- Section 5 --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">5</span>
        {{ __('howto.budget_allocation_step5_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.budget_allocation_step5_desc') }}</p>
    </div>
</section>

{{-- Warning --}}
<div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
    <p class="flex items-start gap-2"><span class="text-amber-500 font-bold">!</span> {{ __('howto.budget_allocation_warn') }}</p>
</div>

@endsection
