@extends('layouts.talent')

@section('title', __('talent.daily_log_new'))
@section('page-title', __('talent.daily_log_new'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('talent.daily-logs.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('talent.back_to_daily_logs') }}
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">{{ __('talent.daily_log_new') }}</h2>
        <p class="text-sm text-gray-500 mb-6">{{ __('talent.daily_log_new_desc') }}</p>

        <form method="POST" action="{{ route('talent.daily-logs.store') }}" class="space-y-5">
            @csrf

            {{-- Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.date') }} <span class="text-red-500">*</span></label>
                <input type="date" name="log_date" value="{{ old('log_date', today()->toDateString()) }}"
                       max="{{ today()->toDateString() }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('log_date') border-red-400 @enderror">
                @error('log_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Mood --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('talent.mood_question') }} <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-5 gap-2">
                    @foreach(['great' => ['😄', __('talent.mood.great')], 'good' => ['🙂', __('talent.mood.good')], 'neutral' => ['😐', __('talent.mood.neutral')], 'tired' => ['😓', __('talent.mood.tired')], 'difficult' => ['😟', __('talent.mood.difficult')]] as $val => [$emoji, $label])
                        <label class="cursor-pointer">
                            <input type="radio" name="mood" value="{{ $val }}" class="sr-only peer" {{ old('mood', 'good') === $val ? 'checked' : '' }}>
                            <div class="flex flex-col items-center gap-1 p-2 rounded-lg border-2 border-gray-200 peer-checked:border-[#1E3A5F] peer-checked:bg-blue-50 hover:border-gray-300 transition-colors text-center">
                                <span class="text-2xl">{{ $emoji }}</span>
                                <span class="text-xs text-gray-600 leading-tight">{{ $label }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('mood') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Activities --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    {{ __('talent.activities_done') }} <span class="text-red-500">*</span>
                </label>
                <textarea name="activities" rows="5"
                          placeholder="{{ __('talent.activities_placeholder') }}"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] resize-none @error('activities') border-red-400 @enderror">{{ old('activities') }}</textarea>
                @error('activities') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Learnings --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('talent.today_learnings') }}</label>
                <textarea name="learnings" rows="3"
                          placeholder="{{ __('talent.learnings_placeholder') }}"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] resize-none">{{ old('learnings') }}</textarea>
            </div>

            {{-- Challenges --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('talent.challenges_faced') }}</label>
                <textarea name="challenges" rows="3"
                          placeholder="{{ __('talent.challenges_placeholder') }}"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] resize-none">{{ old('challenges') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="bg-[#1E3A5F] text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-[#274670] transition-colors">
                    {{ __('talent.save_log') }}
                </button>
                <a href="{{ route('talent.daily-logs.index') }}"
                   class="px-4 py-2.5 rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition-colors">
                    {{ __('talent.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
