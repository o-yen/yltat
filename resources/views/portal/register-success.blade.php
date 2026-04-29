@extends('layouts.public')

@section('title', __('portal.success_page_title') . ' — ' . __('common.protege_programme'))

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-16">
    <div class="max-w-lg w-full text-center">

        <!-- Success Icon -->
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-green-100 mb-6">
            <svg class="w-14 h-14 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <!-- Heading -->
        <h1 class="text-2xl font-bold text-slate-900 mb-2">{{ __('portal.success_heading') }}</h1>
        <p class="text-slate-500 text-sm mb-6">{{ __('portal.success_thank_you') }}</p>

        @if(session('account_email'))
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-left">
            <p class="text-sm text-blue-900 font-semibold mb-1">{{ __('portal.success_account_created') }}</p>
            <p class="text-sm text-blue-800 leading-6">
                {!! __('portal.success_account_email_sent', ['email' => session('account_email')]) !!}
            </p>
        </div>
        @endif

        <!-- Reference Code -->
        @if(session('ref_code'))
        <div class="bg-[#1E3A5F] rounded-2xl p-5 mb-6 text-center">
            <p class="text-blue-200 text-xs mb-1">{{ __('portal.success_ref_code_label') }}</p>
            <p class="text-white text-2xl font-bold tracking-widest">{{ session('ref_code') }}</p>
            <p class="text-blue-300 text-xs mt-1">{{ __('portal.success_ref_code_hint') }}</p>
        </div>
        @endif

        <!-- Info card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 text-left space-y-3">
            <h2 class="text-sm font-semibold text-slate-800 mb-2">{{ __('portal.success_next_steps') }}</h2>

            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-blue-700 text-xs font-bold">1</span>
                </div>
                <p class="text-sm text-slate-600">{!! __('portal.success_step_1') !!}</p>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-blue-700 text-xs font-bold">2</span>
                </div>
                <p class="text-sm text-slate-600">{!! __('portal.success_step_2') !!}</p>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-blue-700 text-xs font-bold">3</span>
                </div>
                <p class="text-sm text-slate-600">{!! __('portal.success_step_3') !!}</p>
            </div>
        </div>

        <!-- Notice -->
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-left">
            <div class="flex items-start gap-2">
                <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs text-amber-700">{!! __('portal.success_spam_notice') !!}</p>
            </div>
        </div>

        <!-- CTA -->
        <a href="{{ route('portal.index') }}"
           class="inline-flex items-center gap-2 bg-[#274670] text-white px-6 py-3 rounded-xl font-semibold text-sm hover:bg-[#1f3a5c] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('portal.success_back_to_portal') }}
        </a>
    </div>
</div>
@endsection
