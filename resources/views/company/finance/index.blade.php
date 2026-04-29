@extends('layouts.company')
@section('title', __('company.finance_title'))
@section('page-title', __('company.finance_page_title'))

@section('content')
<div class="space-y-6">

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-[#1E3A5F] to-[#274670] rounded-xl p-5 text-white col-span-2 lg:col-span-1">
            <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('common.total_allocation') }}</p>
            <p class="text-2xl font-bold mt-1">RM {{ number_format($totalAllocated, 2) }}</p>
            <p class="text-blue-300 text-xs mt-1">{{ __('company.all_batches') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('company.approved') }}</p>
            <p class="text-2xl font-bold text-green-600 mt-1">RM {{ number_format($totalDisbursed, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('company.approved') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('company.in_process') }}</p>
            <p class="text-2xl font-bold text-amber-500 mt-1">RM {{ number_format($totalPending, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('company.pending_approval') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('company.remaining_allocation') }}</p>
            <p class="text-2xl font-bold {{ $remaining >= 0 ? 'text-[#1E3A5F]' : 'text-red-600' }} mt-1">
                RM {{ number_format($remaining, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Peruntukan - Disalurkan</p>
        </div>
    </div>

    {{-- Progress bar --}}
    @if($totalAllocated > 0)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex justify-between items-center mb-2">
                <p class="text-sm font-medium text-gray-700">{{ __('company.budget_usage') }}</p>
                <p class="text-sm font-bold text-[#1E3A5F]">{{ number_format(($totalDisbursed / $totalAllocated) * 100, 1) }}%</p>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                @php $pct = min(100, ($totalDisbursed / $totalAllocated) * 100); @endphp
                <div class="bg-[#1E3A5F] h-3 rounded-full transition-all" style="width: {{ $pct }}%"></div>
            </div>
            <div class="flex justify-between mt-1">
                <p class="text-xs text-gray-400">{{ __('company.used_amount', ['amount' => number_format($totalDisbursed, 2)]) }}</p>
                <p class="text-xs text-gray-400">{{ __('company.allocated_amount', ['amount' => number_format($totalAllocated, 2)]) }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Budget allocations by batch --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('company.allocation_by_batch') }}</h3>
            @if($allocations->count())
                <div class="space-y-3">
                    @foreach($allocations as $alloc)
                        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100">
                            <div>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ $alloc->batch?->batch_name ?? 'Batch ' . $alloc->fiscal_year }}
                                </p>
                                <p class="text-xs text-gray-500">{{ __('company.fiscal_year') }}: {{ $alloc->fiscal_year }}</p>
                            </div>
                            <p class="font-bold text-[#1E3A5F]">RM {{ number_format($alloc->allocated_amount, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm text-center py-6">{{ __('company.no_allocation_records') }}</p>
            @endif
        </div>

        {{-- Per-placement finance --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('company.finance_by_placement') }}</h3>
            @if($placements->count())
                <div class="space-y-3">
                    @foreach($placements as $placement)
                        @php
                            $placed_disbursed = $placement->budgetTransactions->where('status','approved')->sum('amount');
                            $placed_pending   = $placement->budgetTransactions->where('status','pending')->sum('amount');
                        @endphp
                        <a href="{{ route('company.placements.show', $placement) }}"
                           class="block p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm font-medium text-gray-800">{{ $placement->talent?->full_name ?? '—' }}</p>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ in_array($placement->placement_status,['active','confirmed']) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($placement->placement_status) }}
                                </span>
                            </div>
                            <div class="flex gap-4 text-xs text-gray-500">
                                <span>{{ __('company.allowance_per_month') }}: <strong class="text-gray-700">RM {{ number_format($placement->monthly_stipend, 2) }}</strong></span>
                                <span>{{ __('company.approved') }}: <strong class="text-green-600">RM {{ number_format($placed_disbursed, 2) }}</strong></span>
                                @if($placed_pending > 0)
                                    <span>{{ __('company.in_process') }}: <strong class="text-amber-500">RM {{ number_format($placed_pending, 2) }}</strong></span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm text-center py-6">{{ __('company.no_placement_records') }}</p>
            @endif
        </div>
    </div>

    {{-- Full transaction history --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">{{ __('company.transaction_history') }}</h3>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('company.transaction_history_desc') }}</p>
        </div>
        @if($transactions->count())
            <div class="divide-y divide-gray-100">
                @foreach($transactions as $tx)
                    @php
                        $txColor = match($tx->status) {
                            'approved' => ['bg-green-50','text-green-500','text-green-700',__('company.approved')],
                            'pending'  => ['bg-amber-50','text-amber-500','text-amber-700',__('company.in_process')],
                            default    => ['bg-red-50','text-red-500','text-red-700',__('company.rejected')],
                        };
                        $catLabel = match($tx->category) {
                            'stipend' => __('common.monthly_allowance'), 'transport' => __('company.transport'),
                            'meal' => __('company.meal'), default => ucfirst($tx->category ?? '—')
                        };
                    @endphp
                    <div class="flex items-center gap-4 px-5 py-4">
                        <div class="w-9 h-9 rounded-full {{ $txColor[0] }} flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 {{ $txColor[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800">{{ $catLabel }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $tx->placement?->talent?->full_name ?? '—' }} ·
                                {{ $tx->transaction_date->format('d M Y') }}
                                @if($tx->reference_no) · Ref: {{ $tx->reference_no }} @endif
                            </p>
                            @if($tx->description)
                                <p class="text-xs text-gray-400">{{ $tx->description }}</p>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-gray-800">RM {{ number_format($tx->amount, 2) }}</p>
                            <p class="text-xs {{ $txColor[2] }}">{{ $txColor[3] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <p class="text-gray-400">{{ __('messages.no_transactions') }}</p>
            </div>
        @endif
    </div>

</div>
@endsection
