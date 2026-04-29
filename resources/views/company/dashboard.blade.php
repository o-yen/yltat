@extends('layouts.company')
@section('title', __('company.dashboard_title'))
@section('page-title', __('company.dashboard_title'))

@section('content')
<div class="space-y-6">

    {{-- Company header --}}
    <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] rounded-2xl p-6 text-white">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('company.welcome') }}</p>
                <h2 class="text-xl font-bold mt-0.5">{{ auth()->user()->full_name }}</h2>
                <p class="text-blue-300 text-sm mt-0.5">{{ $company->company_name }}</p>
                @if($company->industry)
                    <p class="text-blue-400 text-xs mt-1">{{ $company->industry }}</p>
                @endif
            </div>
            <div class="text-right hidden sm:block">
                <p class="text-blue-200 text-xs">{{ __('company.today_date') }}</p>
                <p class="text-white font-semibold text-sm">{{ now()->translatedFormat('d M Y') }}</p>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 border-t border-white/20">
            <div>
                <p class="text-blue-200 text-xs">{{ __('common.active_placements_label') }}</p>
                <p class="text-white text-2xl font-bold">{{ $activePlacements->count() }}</p>
            </div>
            <div>
                <p class="text-blue-200 text-xs">{{ __('common.status.completed') }}</p>
                <p class="text-white text-2xl font-bold">{{ $completedPlacements->count() }}</p>
            </div>
            <div>
                <p class="text-blue-200 text-xs">{{ __('common.pending_feedback') }}</p>
                <p class="{{ $pendingFeedbackCount > 0 ? 'text-red-300' : 'text-white' }} text-2xl font-bold">{{ $pendingFeedbackCount }}</p>
            </div>
            <div>
                <p class="text-blue-200 text-xs">{{ __('company.remaining_allocation') }}</p>
                <p class="text-white text-xl font-bold">RM {{ number_format(max(0, $totalAllocated - $totalDisbursed), 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Finance summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('common.total_allocation') }}</p>
            <p class="text-2xl font-bold text-[#1E3A5F] mt-1">RM {{ number_format($totalAllocated, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('company.all_batches') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('company.approved') }}</p>
            <p class="text-2xl font-bold text-green-600 mt-1">RM {{ number_format($totalDisbursed, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('company.approved_payments') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('company.in_process') }}</p>
            <p class="text-2xl font-bold text-amber-500 mt-1">RM {{ number_format($totalPending, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('company.pending_approval') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Pending feedback alert --}}
        @if($pendingFeedbackCount > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-amber-800">{{ __('company.feedback_pending_count', ['count' => $pendingFeedbackCount]) }}</p>
                    <p class="text-sm text-amber-700 mt-0.5">{{ __('company.feedback_pending_desc') }}</p>
                    <a href="{{ route('company.feedback.create') }}"
                       class="inline-flex items-center gap-1.5 mt-3 bg-amber-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-amber-700 transition-colors">
                        {{ __('common.submit_feedback') }}
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- Recent placements --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 {{ $pendingFeedbackCount === 0 ? 'lg:col-span-2' : '' }}">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">{{ __('company.recent_placements') }}</h3>
                <a href="{{ route('company.placements.index') }}" class="text-xs text-[#1E3A5F] hover:underline">{{ __('company.view_all') }}</a>
            </div>

            @if($recentPlacements->count())
                <div class="space-y-3">
                    @foreach($recentPlacements as $placement)
                        @php
                            $statusColor = match($placement->placement_status) {
                                'active','confirmed' => 'bg-green-100 text-green-700',
                                'completed' => 'bg-blue-100 text-blue-700',
                                'planned' => 'bg-yellow-100 text-yellow-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <a href="{{ route('company.placements.show', $placement) }}"
                           class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors group">
                            <div class="w-9 h-9 rounded-full bg-[#1E3A5F] flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                {{ substr($placement->talent?->full_name ?? '?', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $placement->talent?->full_name ?? '—' }}</p>
                                <p class="text-xs text-gray-500">{{ $placement->department ?? __('company.no_department') }} · {{ $placement->start_date?->format('M Y') }}</p>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColor }} flex-shrink-0">
                                {{ __('common.status.' . $placement->placement_status) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm text-center py-6">{{ __('company.no_placement_records') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
