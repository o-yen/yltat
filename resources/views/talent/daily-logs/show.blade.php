@extends('layouts.talent')

@section('title', __('talent.daily_log_for_date', ['date' => $dailyLog->log_date->format('d M Y')]))
@section('page-title', __('talent.daily_logs_title'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('talent.daily-logs.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('talent.back') }}
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('talent.daily-logs.edit', $dailyLog) }}"
               class="inline-flex items-center gap-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{ __('talent.edit') }}
            </a>
            <form method="POST" action="{{ route('talent.daily-logs.destroy', $dailyLog) }}"
                  onsubmit="return confirm('{{ __('talent.delete_log_confirm') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 text-sm bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ __('talent.delete') }}
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] px-6 py-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('talent.daily_logs_title') }}</p>
                    <h2 class="text-white text-xl font-bold mt-0.5">{{ $dailyLog->log_date->format('d M Y') }}</h2>
                    <p class="text-blue-300 text-sm mt-0.5">{{ $dailyLog->log_date->translatedFormat('l') }}</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl">
                        {{ match($dailyLog->mood) { 'great' => '😄', 'good' => '🙂', 'neutral' => '😐', 'tired' => '😓', 'difficult' => '😟', default => '📝' } }}
                    </div>
                    <p class="text-blue-200 text-xs mt-1">{{ $dailyLog->mood_label }}</p>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="p-6 space-y-6">

            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">{{ __('talent.activities_done') }}</h3>
                <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $dailyLog->activities }}</div>
            </div>

            @if($dailyLog->learnings)
                <div>
                    <h3 class="text-xs font-semibold text-blue-400 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                        <span>💡</span> {{ __('talent.learning') }}
                    </h3>
                    <div class="bg-blue-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $dailyLog->learnings }}</div>
                </div>
            @endif

            @if($dailyLog->challenges)
                <div>
                    <h3 class="text-xs font-semibold text-amber-500 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                        <span>⚡</span> {{ __('talent.challenges') }}
                    </h3>
                    <div class="bg-amber-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $dailyLog->challenges }}</div>
                </div>
            @endif

            @if($dailyLog->placement)
                <div class="pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400">{{ __('talent.placement_label') }}: <span class="text-gray-600">{{ $dailyLog->placement->company->company_name ?? '-' }}</span></p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
