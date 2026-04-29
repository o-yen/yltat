{{-- Shared layout for how-to guides --}}
@extends('layouts.admin')

@section('title', __('guide.title') . ' — ' . __($howto['title_key']))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <a href="{{ route('admin.system-guide.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ __('guide.back_to_guides') }}
    </a>

    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-xl p-6 text-white">
        <p class="text-indigo-200 text-xs uppercase tracking-wide mb-1">{{ __('howto.section_title') }}</p>
        <h1 class="text-xl font-bold">{{ __($howto['title_key']) }}</h1>
        <p class="text-indigo-200 text-sm mt-1">{{ __($howto['desc_key']) }}</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="p-6 md:p-8 space-y-8 text-sm text-gray-700 leading-relaxed">
            @yield('howto-content')
        </div>
    </div>

    <a href="{{ route('admin.system-guide.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
        &larr; {{ __('guide.back_to_guides') }}
    </a>
</div>
@endsection
