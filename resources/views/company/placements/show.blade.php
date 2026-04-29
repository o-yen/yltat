@extends('layouts.company')
@section('title', $placement->talent?->full_name ?? __('company.placement_details_title'))
@section('page-title', __('company.placement_details_title'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('company.placements.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('company.back_to_placements') }}
        </a>
        @if(! $companyFeedback && in_array($placement->placement_status, ['active','confirmed']))
            <a href="{{ route('company.feedback.create', ['placement_id' => $placement->id]) }}"
               class="inline-flex items-center gap-2 bg-[#C8102E] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('common.submit_feedback') }}
            </a>
        @endif
    </div>

    {{-- Talent + Placement hero --}}
    <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] rounded-2xl p-6 text-white">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                {{ substr($placement->talent?->full_name ?? '?', 0, 1) }}
            </div>
            <div class="flex-1">
                <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('common.talent') }}</p>
                <h2 class="text-xl font-bold">{{ $placement->talent?->full_name ?? '—' }}</h2>
                <p class="text-blue-300 text-sm">{{ $placement->talent?->talent_code }} · {{ $placement->talent?->programme ?? '—' }}</p>
            </div>
            @php
                $statusColor = match($placement->placement_status) {
                    'active','confirmed' => 'bg-green-400/20 text-green-200 border-green-400/30',
                    'completed' => 'bg-blue-400/20 text-blue-200 border-blue-400/30',
                    default => 'bg-gray-400/20 text-gray-200 border-gray-400/30',
                };
            @endphp
            <span class="border px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                {{ ucfirst($placement->placement_status) }}
            </span>
        </div>
        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-white/20">
            <div>
                <p class="text-blue-200 text-xs">{{ __('common.department') }}</p>
                <p class="text-white font-medium text-sm">{{ $placement->department ?? '—' }}</p>
            </div>
            <div>
                <p class="text-blue-200 text-xs">{{ __('common.supervisor_name') }}</p>
                <p class="text-white font-medium text-sm">{{ $placement->supervisor_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-blue-200 text-xs">{{ __('common.period') }}</p>
                <p class="text-white font-medium text-sm">
                    {{ $placement->start_date?->format('d M Y') }} — {{ $placement->end_date?->format('d M Y') }}
                </p>
            </div>
            <div>
                <p class="text-blue-200 text-xs">{{ __('common.monthly_allowance') }}</p>
                <p class="text-white font-bold">RM {{ number_format($placement->monthly_stipend, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Financial breakdown --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('common.tab_finance') }}
            </h3>
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-blue-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">{{ __('company.allowance_per_month') }}</p>
                    <p class="font-bold text-[#1E3A5F] text-sm mt-0.5">RM {{ number_format($placement->monthly_stipend, 2) }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">{{ __('company.approved') }}</p>
                    <p class="font-bold text-green-600 text-sm mt-0.5">RM {{ number_format($totalDisbursed, 2) }}</p>
                </div>
                <div class="bg-amber-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">{{ __('company.in_process') }}</p>
                    <p class="font-bold text-amber-600 text-sm mt-0.5">RM {{ number_format($totalPending, 2) }}</p>
                </div>
            </div>

            @if($transactions->count())
                <div class="border-t border-gray-100 pt-4 space-y-2 max-h-60 overflow-y-auto">
                    @foreach($transactions as $tx)
                        @php
                            $txColor = match($tx->status) {
                                'approved' => 'text-green-600', 'pending' => 'text-amber-500', default => 'text-red-500'
                            };
                            $txLabel = match($tx->status) {
                                'approved' => __('company.approved'), 'pending' => __('company.in_process'), default => __('company.rejected')
                            };
                            $catLabel = match($tx->category) {
                                'stipend' => __('company.monthly_allowance_short'), 'transport' => __('company.transport'), 'meal' => __('company.meal'), default => ucfirst($tx->category ?? '—')
                            };
                        @endphp
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div>
                                <p class="text-sm text-gray-700">{{ $catLabel }}</p>
                                <p class="text-xs text-gray-400">{{ $tx->transaction_date->format('d M Y') }}
                                    @if($tx->reference_no) · {{ $tx->reference_no }} @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800 text-sm">RM {{ number_format($tx->amount, 2) }}</p>
                                <p class="text-xs {{ $txColor }}">{{ $txLabel }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm text-center py-4">{{ __('company.no_transactions_for_placement') }}</p>
            @endif
        </div>

        {{-- Feedback --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                {{ __('nav.feedback') }}
            </h3>

            @if($companyFeedback)
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-xs text-green-700 font-medium">✓ {{ __('company.feedback_submitted_flag') }}</p>
                    <p class="text-xs text-green-600 mt-0.5">{{ $companyFeedback->submitted_at?->format('d M Y, h:i A') }}</p>
                </div>
                @php
                    $scores = [
                        'Teknikal' => $companyFeedback->score_technical,
                        'Komunikasi' => $companyFeedback->score_communication,
                        'Disiplin' => $companyFeedback->score_discipline,
                        'Penyelesaian Masalah' => $companyFeedback->score_problem_solving,
                        'Profesionalisme' => $companyFeedback->score_professionalism,
                    ];
                @endphp
                <div class="space-y-2 mb-3">
                    @foreach($scores as $label => $score)
                        @if($score)
                            <div class="flex items-center gap-3">
                                <p class="text-xs text-gray-500 w-36 flex-shrink-0">{{ $label }}</p>
                                <div class="flex gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <div class="w-5 h-5 rounded {{ $i <= $score ? 'bg-[#1E3A5F]' : 'bg-gray-200' }}"></div>
                                    @endfor
                                </div>
                                <span class="text-xs font-semibold text-gray-700">{{ $score }}/5</span>
                            </div>
                        @endif
                    @endforeach
                </div>
                @if($companyFeedback->average_score)
                    <div class="flex items-center justify-between bg-[#1E3A5F] text-white rounded-lg px-3 py-2 mb-3">
                        <span class="text-sm">{{ __('company.average_score_label') }}</span>
                        <span class="font-bold">{{ $companyFeedback->average_score }}/5</span>
                    </div>
                @endif
                @if($companyFeedback->comments)
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">{{ __('common.comments') }}</p>
                        <p class="text-sm text-gray-700">{{ $companyFeedback->comments }}</p>
                    </div>
                @endif
            @elseif(in_array($placement->placement_status, ['active','confirmed']))
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-gray-600 font-medium text-sm">{{ __('company.placement_feedback_not_submitted') }}</p>
                    <a href="{{ route('company.feedback.create', ['placement_id' => $placement->id]) }}"
                       class="mt-3 inline-flex items-center gap-1.5 bg-[#1E3A5F] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#274670] transition-colors">
                        {{ __('company.submit_now') }}
                    </a>
                </div>
            @else
                <p class="text-gray-400 text-sm text-center py-6">{{ __('company.no_feedback_for_placement') }}</p>
            @endif
        </div>
    </div>

    {{-- Talent profile info --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('company.talent_information') }}</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400">Universiti</p>
                <p class="text-gray-800 mt-0.5">{{ $placement->talent?->university ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Program</p>
                <p class="text-gray-800 mt-0.5">{{ $placement->talent?->programme ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">CGPA</p>
                <p class="text-gray-800 mt-0.5">{{ $placement->talent?->cgpa ? number_format($placement->talent->cgpa, 2) : '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Telefon</p>
                <p class="text-gray-800 mt-0.5">{{ $placement->talent?->phone ?? '—' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
