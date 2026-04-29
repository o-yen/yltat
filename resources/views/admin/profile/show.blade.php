@extends('layouts.admin')

@section('title', __('common.profile_title'))
@section('page-title', __('common.profile_title'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Profile card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Header banner --}}
        <div class="h-24 bg-gradient-to-r from-[#1E3A5F] to-[#2d5289]"></div>

        {{-- Avatar + actions --}}
        <div class="px-6 pb-6">
            <div class="flex items-end justify-between -mt-10 mb-4">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" class="w-20 h-20 rounded-full object-cover shadow-lg ring-4 ring-white">
                @else
                    <div class="w-20 h-20 rounded-full bg-[#C8102E] flex items-center justify-center text-white text-3xl font-bold shadow-lg ring-4 ring-white">
                        {{ strtoupper(substr($user->full_name, 0, 1)) }}
                    </div>
                @endif
                <a href="{{ route('admin.profile.edit') }}"
                   class="flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white text-sm font-medium rounded-lg hover:bg-[#162d4a] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    {{ __('common.edit_profile') }}
                </a>
            </div>

            <h2 class="text-xl font-bold text-gray-900">{{ $user->full_name }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $user->role?->role_name }}</p>
        </div>
    </div>

    {{-- Account info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">{{ __('common.account_info') }}</h3>

        <dl class="space-y-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <dt class="text-xs text-gray-400">{{ __('common.full_name') }}</dt>
                    <dd class="text-sm font-medium text-gray-800 mt-0.5">{{ $user->full_name }}</dd>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <dt class="text-xs text-gray-400">{{ __('common.email') }}</dt>
                    <dd class="text-sm font-medium text-gray-800 mt-0.5">{{ $user->email }}</dd>
                </div>
            </div>

            @if($user->phone)
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <div>
                    <dt class="text-xs text-gray-400">{{ __('common.phone_no') }}</dt>
                    <dd class="text-sm font-medium text-gray-800 mt-0.5">{{ $user->phone }}</dd>
                </div>
            </div>
            @endif

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <dt class="text-xs text-gray-400">{{ __('common.role') }}</dt>
                    <dd class="text-sm font-medium text-gray-800 mt-0.5">{{ $user->role?->role_name ?? '—' }}</dd>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <dt class="text-xs text-gray-400">{{ __('common.member_since') }}</dt>
                    <dd class="text-sm font-medium text-gray-800 mt-0.5">{{ $user->created_at->format('d M Y') }}</dd>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                </div>
                <div>
                    <dt class="text-xs text-gray-400">{{ __('common.language_label') }}</dt>
                    <dd class="text-sm font-medium text-gray-800 mt-0.5">{{ $user->language === 'ms' ? 'Bahasa Melayu' : 'English' }}</dd>
                </div>
            </div>
        </dl>
    </div>

    {{-- Security --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">{{ __('common.security') }}</h3>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-800">{{ __('common.change_password') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('common.change_password_hint') }}</p>
            </div>
            <a href="{{ route('admin.profile.change-password') }}"
               class="flex items-center gap-2 px-4 py-2 border border-[#C8102E] text-[#C8102E] text-sm font-medium rounded-lg hover:bg-red-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                {{ __('common.change_password') }}
            </a>
        </div>
    </div>

</div>
@endsection
