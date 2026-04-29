@extends('layouts.admin')

@section('title', __('common.change_password'))
@section('page-title', __('common.change_password'))

@section('content')
<div class="max-w-lg mx-auto">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#1E3A5F] to-[#2d5289] px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-white">{{ __('common.change_password') }}</h2>
                    <p class="text-xs text-blue-200 mt-0.5">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <div class="p-6" x-data="{ step: {{ $otpValid ? 3 : ($otpSent ? 2 : 1) }} }">

            {{-- Step indicators --}}
            <div class="flex items-center gap-2 mb-6">
                @foreach([
                    [1, __('common.otp_step_request')],
                    [2, __('common.otp_step_verify')],
                    [3, __('common.otp_step_set')],
                ] as [$num, $label])
                <div class="flex items-center {{ !$loop->first ? 'flex-1' : '' }}">
                    @if(!$loop->first)
                        <div class="flex-1 h-px mx-2"
                             :class="{{ $num }} <= step ? 'bg-[#1E3A5F]' : 'bg-gray-200'"></div>
                    @endif
                    <div class="flex flex-col items-center">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-colors"
                             :class="{{ $num }} <= step ? 'bg-[#1E3A5F] text-white' : 'bg-gray-200 text-gray-400'">
                            <template x-if="{{ $num }} < step">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <template x-if="{{ $num }} >= step">
                                <span>{{ $num }}</span>
                            </template>
                        </div>
                        <span class="text-xs mt-1 whitespace-nowrap"
                              :class="{{ $num }} <= step ? 'text-[#1E3A5F] font-medium' : 'text-gray-400'">
                            {{ $label }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ── STEP 1: Request OTP ── --}}
            <div x-show="step === 1">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-5">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm text-blue-800 font-medium">{{ __('common.otp_info_title') }}</p>
                            <p class="text-xs text-blue-600 mt-1">
                                {!! __('common.otp_info_body', ['email' => '<strong>' . $user->email . '</strong>']) !!}
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.profile.request-otp') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-[#1E3A5F] text-white text-sm font-medium rounded-lg hover:bg-[#162d4a] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ __('common.send_otp') }}
                    </button>
                </form>
            </div>

            {{-- ── STEP 2: Enter OTP ── --}}
            <div x-show="step === 2">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-5">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm text-green-800 font-medium">{{ __('common.otp_sent_title') }}</p>
                            <p class="text-xs text-green-600 mt-1">
                                {!! __('common.otp_sent_body', ['email' => '<strong>' . $user->email . '</strong>']) !!}
                                {{ __('common.otp_expires_hint') }}
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.profile.verify-otp') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('common.enter_otp') }}
                        </label>
                        <input type="text"
                               name="otp"
                               maxlength="6"
                               inputmode="numeric"
                               pattern="\d{6}"
                               placeholder="000000"
                               class="w-full px-3 py-3 border border-gray-300 rounded-lg text-center text-2xl font-mono tracking-[0.5em] focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent @error('otp') border-red-400 @enderror"
                               autofocus required>
                        @error('otp')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full px-5 py-3 bg-[#1E3A5F] text-white text-sm font-medium rounded-lg hover:bg-[#162d4a] transition-colors">
                        {{ __('common.verify_otp') }}
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <form method="POST" action="{{ route('admin.profile.request-otp') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-gray-400 hover:text-[#1E3A5F] underline">
                            {{ __('common.resend_otp') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── STEP 3: Set new password ── --}}
            <div x-show="step === 3">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-5">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-green-800">{{ __('common.otp_verified_msg') }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.profile.update-password') }}" class="space-y-4"
                      x-data="{ showNew: false, showConfirm: false }">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('common.new_password') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showNew ? 'text' : 'password'"
                                   name="new_password"
                                   class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent @error('new_password') border-red-400 @enderror"
                                   required minlength="8" autocomplete="new-password">
                            <button type="button" @click="showNew = !showNew"
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                <svg x-show="!showNew" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showNew" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">{{ __('common.password_min_hint') }}</p>
                        @error('new_password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('common.confirm_new_password') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'"
                                   name="new_password_confirmation"
                                   class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent"
                                   required autocomplete="new-password">
                            <button type="button" @click="showConfirm = !showConfirm"
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                        <p class="text-xs text-amber-700">
                            ⚠️ {{ __('common.relogin_warning') }}
                        </p>
                    </div>

                    <button type="submit"
                            class="w-full px-5 py-3 bg-[#C8102E] text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                        {{ __('common.update_password') }}
                    </button>
                </form>
            </div>

            {{-- Back link --}}
            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <a href="{{ route('admin.profile.show') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                    ← {{ __('common.back_to_profile') }}
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
