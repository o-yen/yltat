@extends('admin.system-guide.howto._layout')

@section('howto-content')

{{-- Section 1: Update Your Profile --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">1</span>
        {{ __('howto.profile_password_step1_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.profile_password_step1_desc') }}</p>
    </div>
</section>

{{-- Section 2: Change Your Password --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">2</span>
        {{ __('howto.profile_password_step2_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.profile_password_step2_desc') }}</p>
    </div>
</section>

{{-- Section 3: Admin Reset --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">3</span>
        {{ __('howto.profile_password_step3_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.profile_password_step3_desc') }}</p>
    </div>
</section>

{{-- Info --}}
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
    <p class="text-blue-800">{{ __('howto.profile_password_info') }}</p>
</div>

{{-- Warning --}}
<div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
    <p class="flex items-start gap-2"><span class="text-amber-500 font-bold">!</span> {{ __('howto.profile_password_warn') }}</p>
</div>

@endsection
