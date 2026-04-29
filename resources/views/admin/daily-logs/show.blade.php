@extends('layouts.admin')

@section('title', __('nav.daily_logs') . ' — ' . $dailyLog->log_date->format('d M Y'))

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Back link --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.daily-logs.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('messages.back_to_list') }}
        </a>
    </div>

    {{-- Header Card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] px-6 py-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('nav.daily_logs') }}</p>
                    <h2 class="text-white text-xl font-bold mt-0.5">{{ $dailyLog->talent->full_name ?? '-' }}</h2>
                    <p class="text-blue-300 text-sm mt-0.5">
                        {{ $dailyLog->talent->id_graduan ?? '' }} &middot;
                        {{ $dailyLog->log_date->format('d M Y') }} ({{ $dailyLog->log_date->translatedFormat('l') }})
                    </p>
                </div>
                <div class="text-center">
                    @php
                        $moodEmoji = match($dailyLog->mood) {
                            'great' => '😄', 'good' => '🙂', 'neutral' => '😐',
                            'tired' => '😓', 'difficult' => '😟', default => '📝'
                        };
                    @endphp
                    <div class="text-4xl">{{ $moodEmoji }}</div>
                    <p class="text-blue-200 text-xs mt-1">{{ $dailyLog->mood_label }}</p>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="p-6 space-y-6">

            {{-- Activities --}}
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">{{ __('messages.activities_done') }}</h3>
                <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $dailyLog->activities }}</div>
            </div>

            {{-- Learnings --}}
            @if($dailyLog->learnings)
                <div>
                    <h3 class="text-xs font-semibold text-blue-400 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                        <span>💡</span> {{ __('messages.learnings') }}
                    </h3>
                    <div class="bg-blue-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $dailyLog->learnings }}</div>
                </div>
            @endif

            {{-- Challenges --}}
            @if($dailyLog->challenges)
                <div>
                    <h3 class="text-xs font-semibold text-amber-500 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                        <span>⚡</span> {{ __('messages.challenges') }}
                    </h3>
                    <div class="bg-amber-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $dailyLog->challenges }}</div>
                </div>
            @endif

            {{-- Placement Info --}}
            @if($dailyLog->placement)
                <div class="pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400">{{ __('messages.placement_company') }}: <span class="text-gray-600">{{ $dailyLog->placement->company->company_name ?? '-' }}</span></p>
                </div>
            @endif

            {{-- Submission Info --}}
            <div class="pt-4 border-t border-gray-100 grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-400 text-xs">{{ __('messages.submitted_at') }}</span>
                    <p class="text-gray-700">{{ $dailyLog->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <span class="text-gray-400 text-xs">{{ __('messages.status') }}</span>
                    <p>
                        <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full {{ $dailyLog->status === 'submitted' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($dailyLog->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Review Section --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">{{ __('messages.admin_review') }}</h3>
        </div>
        <div class="p-6">
            @if($dailyLog->reviewed_at)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium text-green-700">{{ __('messages.reviewed_by_admin') }}</span>
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p><span class="text-gray-400">{{ __('messages.reviewer') }}:</span> {{ $dailyLog->reviewed_by }}</p>
                        <p><span class="text-gray-400">{{ __('messages.reviewed_on') }}:</span> {{ \Carbon\Carbon::parse($dailyLog->reviewed_at)->format('d M Y, H:i') }}</p>
                        @if($dailyLog->admin_remarks)
                            <p class="mt-2"><span class="text-gray-400">{{ __('messages.remarks') }}:</span></p>
                            <div class="bg-white rounded-lg p-3 mt-1 text-gray-700 whitespace-pre-wrap">{{ $dailyLog->admin_remarks }}</div>
                        @endif
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.daily-logs.review', $dailyLog) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('messages.admin_remarks_label') }}
                    </label>
                    <textarea name="admin_remarks" rows="3"
                              placeholder="{{ __('messages.admin_remarks_placeholder') }}"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] resize-none @error('admin_remarks') border-red-400 @enderror">{{ old('admin_remarks', $dailyLog->admin_remarks) }}</textarea>
                    @error('admin_remarks') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="bg-[#1E3A5F] text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-[#274670] transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $dailyLog->reviewed_at ? __('messages.update_review') : __('messages.mark_reviewed') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
