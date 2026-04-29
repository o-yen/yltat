@extends('layouts.talent')

@section('title', __('talent.profile_edit_title'))
@section('page-title', __('talent.profile_edit_title'))

@section('content')
<div class="max-w-2xl">
    <div class="mb-4">
        <a href="{{ route('talent.profile.show') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('talent.back_to_profile') }}
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">{{ __('talent.update_profile') }}</h2>
        <p class="text-sm text-gray-500 mb-6">{{ __('talent.profile_edit_desc') }}</p>

        <form method="POST" action="{{ route('talent.profile.update') }}" class="space-y-5">
            @csrf @method('PUT')

            {{-- Read-only fields --}}
            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ __('talent.fixed_info') }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-gray-400">{{ __('talent.full_name') }}</p>
                        <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $talent->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">{{ __('talent.talent_code') }}</p>
                        <p class="text-sm font-medium text-gray-700 mt-0.5 font-mono">{{ $talent->talent_code }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">{{ __('common.email') }}</p>
                        <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $talent->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">{{ __('talent.ic_passport_no') }}</p>
                        <p class="text-sm font-medium text-gray-700 mt-0.5 font-mono">{{ $talent->ic_passport_no }}</p>
                    </div>
                </div>
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    {{ __('talent.phone_number') }} <span class="text-red-500">*</span>
                </label>
                <input type="tel" name="phone" value="{{ old('phone', $talent->phone) }}"
                       placeholder="Contoh: 0123456789"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('phone') border-red-400 @enderror">
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Address --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('talent.current_address') }}</label>
                <textarea name="address" rows="3"
                          placeholder="{{ __('talent.current_address') }}"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] resize-none">{{ old('address', $talent->address) }}</textarea>
            </div>

            {{-- Skills --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('talent.skills') }}</label>
                <input type="text" name="skills_text" value="{{ old('skills_text', $talent->skills_text) }}"
                       placeholder="Contoh: Microsoft Office, Python, Komunikasi, Kepimpinan"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <p class="text-xs text-gray-400 mt-1">{{ __('talent.skills_hint') }}</p>
            </div>

            {{-- Profile summary --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('talent.profile_summary') }}</label>
                <textarea name="profile_summary" rows="4"
                          placeholder="{{ __('talent.profile_summary_hint') }}"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] resize-none">{{ old('profile_summary', $talent->profile_summary) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">{{ __('talent.profile_summary_max') }}</p>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="bg-[#1E3A5F] text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-[#274670] transition-colors">
                    {{ __('talent.save_changes') }}
                </button>
                <a href="{{ route('talent.profile.show') }}"
                   class="px-4 py-2.5 rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition-colors">
                    {{ __('talent.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
