@extends('layouts.talent')

@section('title', __('talent.allowance_title'))
@section('page-title', __('talent.allowance_title'))

@section('content')
<div class="space-y-6">

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-[#1E3A5F] to-[#274670] rounded-xl p-5 text-white">
            <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('talent.allowance_monthly') }}</p>
            <p class="text-2xl font-bold mt-1">RM {{ number_format($monthlyStipend, 2) }}</p>
            <p class="text-blue-300 text-xs mt-1">{{ $activePlacement ? __('talent.active_placement_short') : __('talent.no_placement') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-gray-500 text-xs uppercase tracking-wide">{{ __('talent.total_received') }}</p>
            <p class="text-2xl font-bold text-green-600 mt-1">RM {{ number_format($totalApproved, 2) }}</p>
            <p class="text-gray-400 text-xs mt-1">{{ __('talent.approved_payment') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-gray-500 text-xs uppercase tracking-wide">{{ __('talent.in_process') }}</p>
            <p class="text-2xl font-bold text-amber-500 mt-1">RM {{ number_format($totalPending, 2) }}</p>
            <p class="text-gray-400 text-xs mt-1">{{ __('talent.pending_approval') }}</p>
        </div>
    </div>

    {{-- Transaction list --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">{{ __('talent.payment_records') }}</h3>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('talent.payment_records_desc') }}</p>
        </div>

        @if($transactions->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($transactions as $tx)
                    @php
                        $statusColor = match($tx->status) {
                            'approved' => 'bg-green-100 text-green-700',
                            'pending'  => 'bg-amber-100 text-amber-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            default    => 'bg-gray-100 text-gray-600',
                        };
                        $statusLabel = match($tx->status) {
                            'approved' => __('company.approved'),
                            'pending'  => __('talent.in_process'),
                            'rejected' => __('company.rejected'),
                            default    => ucfirst($tx->status),
                        };
                        $categoryLabel = match($tx->category) {
                            'stipend'   => __('talent.allowance_monthly'),
                            'transport' => __('talent.allowance_transport'),
                            'meal'      => __('talent.allowance_meal'),
                            'other'     => __('talent.other'),
                            default     => ucfirst($tx->category ?? '—'),
                        };
                    @endphp
                    <div class="flex items-center gap-4 px-5 py-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                            {{ $tx->status === 'approved' ? 'bg-green-50' : ($tx->status === 'pending' ? 'bg-amber-50' : 'bg-red-50') }}">
                            @if($tx->status === 'approved')
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @elseif($tx->status === 'pending')
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800">{{ $categoryLabel }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $tx->transaction_date->format('d M Y') }}
                                @if($tx->reference_no) · Ref: {{ $tx->reference_no }} @endif
                            </p>
                            @if($tx->description)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $tx->description }}</p>
                            @endif
                        </div>

                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-gray-800">RM {{ number_format($tx->amount, 2) }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-center px-6">
                <div class="w-14 h-14 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">{{ __('talent.no_payment_records') }}</p>
                <p class="text-gray-400 text-sm mt-1">{{ __('talent.payment_records_note') }}</p>
            </div>
        @endif
    </div>

    {{-- Note --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-blue-700 text-sm">
            {{ __('talent.allowance_help_note') }}
        </p>
    </div>

</div>
@endsection
