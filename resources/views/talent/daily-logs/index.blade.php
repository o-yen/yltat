@extends('layouts.talent')

@section('title', __('talent.daily_logs_title'))
@section('page-title', __('talent.daily_logs_title'))

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">{{ __('talent.daily_logs_my') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('talent.daily_logs_desc') }}</p>
        </div>
        @if(! $todayLogged)
            <a href="{{ route('talent.daily-logs.create') }}"
               class="inline-flex items-center gap-2 bg-[#1E3A5F] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#274670] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('talent.log_today') }}
            </a>
        @else
            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-200 px-3 py-2 rounded-lg text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('talent.today_log_recorded') }}
            </span>
        @endif
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <form method="GET" class="flex items-center gap-3">
            <label class="text-sm text-gray-600 font-medium">{{ __('talent.filter_by_month') }}</label>
            <input type="month" name="month" value="{{ request('month') }}"
                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <button type="submit" class="bg-[#1E3A5F] text-white px-3 py-1.5 rounded-lg text-sm hover:bg-[#274670] transition-colors">
                {{ __('talent.filter') }}
            </button>
            @if(request('month'))
                <a href="{{ route('talent.daily-logs.index') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('talent.clear_filter') }}</a>
            @endif
        </form>
    </div>

    {{-- Log list --}}
    <div class="space-y-3">
        @forelse($logs as $log)
            @php
                $moodEmoji  = match($log->mood) { 'great' => '😄', 'good' => '🙂', 'neutral' => '😐', 'tired' => '😓', 'difficult' => '😟', default => '📝' };
                $moodColor  = match($log->mood) { 'great' => 'green', 'good' => 'blue', 'neutral' => 'gray', 'tired' => 'yellow', 'difficult' => 'red', default => 'gray' };
                $isToday    = $log->log_date->isToday();
            @endphp
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:border-[#1E3A5F]/20 transition-colors">
                <div class="flex items-start gap-4">
                    <div class="text-2xl leading-none mt-1 flex-shrink-0">{{ $moodEmoji }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-gray-800">
                                {{ $log->log_date->format('d M Y') }}
                                @if($isToday) <span class="text-xs font-normal text-[#C8102E] ml-1">({{ __('talent.today_label') }})</span> @endif
                            </span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $moodColor }}-100 text-{{ $moodColor }}-700">
                                {{ $log->mood_label }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $log->activities }}</p>
                        @if($log->learnings)
                            <p class="text-xs text-blue-600 mt-1">💡 {{ Str::limit($log->learnings, 80) }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('talent.daily-logs.show', $log) }}"
                           class="text-gray-400 hover:text-[#1E3A5F] p-1.5 rounded-lg hover:bg-gray-50 transition-colors" title="{{ __('talent.details') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('talent.daily-logs.edit', $log) }}"
                           class="text-gray-400 hover:text-[#1E3A5F] p-1.5 rounded-lg hover:bg-gray-50 transition-colors" title="{{ __('talent.edit') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('talent.daily-logs.destroy', $log) }}"
                              onsubmit="return confirm('{{ __('talent.delete_log_confirm') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500 p-1.5 rounded-lg hover:bg-red-50 transition-colors" title="{{ __('talent.delete') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">{{ __('talent.no_logs_found') }}</p>
                <p class="text-gray-400 text-sm mt-1">{{ __('talent.start_logging_today') }}</p>
                <a href="{{ route('talent.daily-logs.create') }}"
                   class="mt-4 inline-flex items-center gap-2 bg-[#1E3A5F] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#274670] transition-colors">
                    {{ __('talent.add_log') }}
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
        <div>{{ $logs->links() }}</div>
    @endif
</div>
@endsection
