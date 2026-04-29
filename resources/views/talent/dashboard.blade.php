@extends('layouts.talent')

@section('title', __('talent.dashboard_title'))
@section('page-title', __('talent.dashboard_title'))

@section('content')
<div class="space-y-6">

    {{-- Welcome header --}}
    <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] rounded-2xl p-6 text-white">
        <div class="flex items-center gap-4">
            @if($talent->profile_photo)
                <img src="{{ Storage::url($talent->profile_photo) }}" class="w-16 h-16 rounded-full object-cover border-2 border-white/30">
            @else
                <div class="w-16 h-16 rounded-full bg-[#C8102E] flex items-center justify-center text-white text-2xl font-bold border-2 border-white/30">
                    {{ substr($talent->full_name, 0, 1) }}
                </div>
            @endif
            <div>
                <p class="text-blue-200 text-sm">{{ __('company.welcome') }}</p>
                <h2 class="text-xl font-bold">{{ $talent->full_name }}</h2>
                <p class="text-blue-300 text-sm mt-0.5">{{ $talent->talent_code }}</p>
            </div>
            <div class="ml-auto text-right hidden sm:block">
                <p class="text-blue-200 text-xs">{{ __('company.today_date') }}</p>
                <p class="text-white font-semibold">{{ now()->translatedFormat('d M Y') }}</p>
            </div>
        </div>

        {{-- Application status banner --}}
        <div class="mt-4 pt-4 border-t border-white/20 flex items-center gap-3">
            <div class="flex-1">
                <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('talent.application_status') }}</p>
                @php
                    $statusColors = [
                        'approved' => 'bg-green-400/20 text-green-200 border border-green-400/40',
                        'applied'  => 'bg-yellow-400/20 text-yellow-200 border border-yellow-400/40',
                        'rejected' => 'bg-red-400/20 text-red-200 border border-red-400/40',
                        'inactive' => 'bg-gray-400/20 text-gray-200 border border-gray-400/40',
                    ];
                    $statusLabels = [
                        'approved' => __('talent.status_labels.approved'),
                        'applied'  => __('talent.status_labels.applied'),
                        'rejected' => __('talent.status_labels.rejected'),
                        'inactive' => __('talent.status_labels.inactive'),
                    ];
                @endphp
                <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$talent->status] ?? 'bg-gray-400/20 text-gray-200' }}">
                    {{ $statusLabels[$talent->status] ?? ucfirst($talent->status) }}
                </span>
            </div>
            @if($activePlacement)
                <div class="text-right">
                    <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('talent.company') }}</p>
                    <p class="text-white font-medium text-sm">{{ $activePlacement->company->company_name ?? '-' }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">{{ __('talent.log_today') }}</p>
            <p class="mt-1 text-2xl font-bold {{ $todayLogged ? 'text-green-600' : 'text-gray-400' }}">
                {{ $todayLogged ? '✓' : '—' }}
            </p>
            <p class="text-xs text-gray-400 mt-1">{{ $todayLogged ? __('talent.today_logged') : __('talent.today_not_logged') }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">{{ __('talent.this_month_logs') }}</p>
            <p class="mt-1 text-2xl font-bold text-[#1E3A5F]">{{ $thisMonthLogs }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ now()->translatedFormat('F Y') }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">{{ __('talent.total_logs') }}</p>
            <p class="mt-1 text-2xl font-bold text-[#1E3A5F]">{{ $totalLogs }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('talent.overall') }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">{{ __('talent.latest_allowance') }}</p>
            @if($latestAllowance)
                <p class="mt-1 text-xl font-bold text-[#1E3A5F]">RM {{ number_format($latestAllowance->amount, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $latestAllowance->transaction_date->format('M Y') }}</p>
            @else
                <p class="mt-1 text-2xl font-bold text-gray-400">—</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('talent.no_record') }}</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Today's log prompt --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">{{ __('talent.daily_logs_title') }}</h3>
                <a href="{{ route('talent.daily-logs.index') }}" class="text-xs text-[#1E3A5F] hover:underline">{{ __('talent.view_all') }}</a>
            </div>

            @if(! $todayLogged)
                <div class="flex flex-col items-center justify-center py-6 text-center">
                    <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-gray-600 text-sm font-medium">{{ __('talent.you_have_not_logged_today') }}</p>
                    <p class="text-gray-400 text-xs mt-1">{{ __('talent.log_your_activity_prompt') }}</p>
                    <a href="{{ route('talent.daily-logs.create') }}"
                       class="mt-4 inline-flex items-center gap-2 bg-[#1E3A5F] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#274670] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('talent.add_today_log') }}
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($recentLogs as $log)
                        <a href="{{ route('talent.daily-logs.show', $log) }}"
                           class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 text-lg">
                                {{ match($log->mood) {
                                    'great' => '😄', 'good' => '🙂', 'neutral' => '😐', 'tired' => '😓', 'difficult' => '😟', default => '📝'
                                } }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800">{{ $log->log_date->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Str::limit($log->activities, 60) }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Placement info card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">{{ __('talent.placement_info_title') }}</h3>
                <a href="{{ route('talent.placement.index') }}" class="text-xs text-[#1E3A5F] hover:underline">{{ __('talent.details') }}</a>
            </div>

            @if($activePlacement)
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">{{ __('talent.company') }}</p>
                            <p class="text-sm font-medium text-gray-800">{{ $activePlacement->company->company_name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">{{ __('talent.supervising_officer') }}</p>
                            <p class="text-sm font-medium text-gray-800">{{ $activePlacement->supervisor_name ?? '-' }}</p>
                            @if($activePlacement->supervisor_email)
                                <p class="text-xs text-gray-400">{{ $activePlacement->supervisor_email }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">{{ __('talent.placement_period') }}</p>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $activePlacement->start_date?->format('d M Y') }} — {{ $activePlacement->end_date?->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-2 pt-3 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">{{ __('talent.monthly_allowance') }}</span>
                            <span class="text-sm font-bold text-green-600">RM {{ number_format($activePlacement->monthly_stipend, 2) }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm">{{ __('talent.no_active_placement') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
