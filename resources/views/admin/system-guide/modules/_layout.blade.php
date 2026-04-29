{{-- Shared layout for module guides --}}
@extends('layouts.admin')

@section('title', __('guide.title') . ' — ' . __($mod['name_key']))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Back --}}
    <a href="{{ route('admin.system-guide.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ __('guide.back_to_guides') }}
    </a>

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] rounded-xl p-6 text-white">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-lg bg-white/10 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @yield('guide-icon')
                </svg>
            </div>
            <div>
                <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('guide.module_guide') }}</p>
                <h1 class="text-xl font-bold">{{ __($mod['name_key']) }}</h1>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="p-6 md:p-8 space-y-8 text-sm text-gray-700 leading-relaxed">
            @yield('guide-content')
        </div>
    </div>

    {{-- Navigation --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.system-guide.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; {{ __('guide.back_to_guides') }}</a>
        <a href="{{ route($mod['route']) }}" class="inline-flex items-center gap-2 bg-[#1E3A5F] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#274670] transition-colors">
            {{ __('guide.go_to_module') }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    </div>
</div>
@endsection
