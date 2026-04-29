@extends('layouts.admin')
@section('title', __('nav.budget'))
@section('page-title', __('nav.budget'))

@section('content')
<!-- KPI Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="text-xs text-gray-500 uppercase font-semibold mb-1">{{ __('common.allocation_year', ['year' => $currentYear]) }}</div>
        <div class="text-2xl font-bold text-gray-800">RM {{ number_format($totalAllocated, 0) }}</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="text-xs text-gray-500 uppercase font-semibold mb-1">{{ __('common.spent') }}</div>
        <div class="text-2xl font-bold text-red-700">RM {{ number_format($totalSpent, 0) }}</div>
        <div class="text-xs text-gray-400 mt-1">
            {{ __('common.budget_categories.allowance') }}: RM {{ number_format($kewanganPaid, 0) }}
            @if($manualSpent > 0)
                · {{ __('common.budget_categories.other') }}: RM {{ number_format($manualSpent, 0) }}
            @endif
        </div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="text-xs text-gray-500 uppercase font-semibold mb-1">{{ __('common.actual_balance') }}</div>
        <div class="text-2xl font-bold {{ $actualRemaining >= 0 ? 'text-green-700' : 'text-red-700' }}">
            RM {{ number_format(abs($actualRemaining), 0) }}
        </div>
        @if($kewanganPending > 0)
            <div class="text-xs text-amber-600 mt-1">{{ __('common.in_progress_label') }}: RM {{ number_format($kewanganPending, 0) }}</div>
        @endif
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 {{ $isOverrun ? 'border-red-200 bg-red-50' : '' }}">
        <div class="text-xs text-gray-500 uppercase font-semibold mb-1">{{ __('common.forecast_total') }}{{ $isOverrun ? ' ⚠ ' . __('common.overrun_label') : '' }}</div>
        <div class="text-2xl font-bold {{ $isOverrun ? 'text-red-700' : 'text-blue-700' }}">
            RM {{ number_format($forecastTotal, 0) }}
        </div>
    </div>
</div>

@if($isOverrun)
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
        <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="font-semibold text-red-800">{{ __('messages.budget_overrun_warning') }}</p>
            <p class="text-red-700 text-sm">{{ __('messages.budget_overrun_detail', ['forecast' => number_format($forecastTotal, 2), 'allocated' => number_format($totalAllocated, 2), 'excess' => number_format($forecastTotal - $totalAllocated, 2)]) }}</p>
        </div>
    </div>
@endif

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('common.monthly_spend_trend', ['year' => $currentYear]) }}</h3>
        <div id="chart-trend"></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('common.spend_by_company') }}</h3>
        <div id="chart-company"></div>
    </div>
</div>

<!-- Quick Links -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">{{ __('common.allocation_year', ['year' => $currentYear]) }}</h3>
            <a href="{{ route('admin.budget.allocations') }}" class="text-sm text-blue-600 hover:text-blue-800">{{ __('common.manage_allocations') }}</a>
        </div>
        @foreach($allocations->take(5) as $alloc)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div>
                    <div class="text-sm text-gray-700">
                        {{ $alloc->company?->company_name ?? __('common.general') }}
                    </div>
                    @if($alloc->batch)
                        <div class="text-xs text-gray-400">{{ $alloc->batch->batch_name }}</div>
                    @endif
                </div>
                <div class="text-sm font-semibold text-gray-800">RM {{ number_format($alloc->allocated_amount, 0) }}</div>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">{{ __('common.recent_transactions') }}</h3>
            <a href="{{ route('admin.budget.transactions') }}" class="text-sm text-blue-600 hover:text-blue-800">{{ __('common.all_transactions') }}</a>
        </div>
        @foreach($recentTransactions as $txn)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div>
                    <div class="text-sm text-gray-700">{{ $txn['label'] }}</div>
                    <div class="text-xs text-gray-400">{{ $txn['sub'] }}</div>
                </div>
                <div class="text-sm font-semibold text-red-700">RM {{ number_format($txn['amount'], 2) }}</div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
new ApexCharts(document.querySelector('#chart-trend'), {
    series: [{ name: '{{ __('common.spend_rm') }}', data: @json($trendData) }],
    chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter' },
    colors: ['#1E3A5F'],
    plotOptions: { bar: { borderRadius: 5, columnWidth: '55%' } },
    dataLabels: { enabled: false },
    xaxis: { categories: @json($trendLabels), labels: { style: { fontSize: '11px' } } },
    yaxis: { labels: { formatter: v => 'RM ' + v.toLocaleString('ms-MY'), style: { fontSize: '11px' } } },
    grid: { borderColor: '#f1f5f9' }
}).render();

@if(count($companyLabels) > 0)
new ApexCharts(document.querySelector('#chart-company'), {
    series: [{ name: '{{ __('common.spend_rm') }}', data: @json($companyData) }],
    chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter' },
    colors: ['#C8102E'],
    plotOptions: { bar: { borderRadius: 5, horizontal: true } },
    dataLabels: { enabled: false },
    xaxis: { categories: @json($companyLabels), labels: { formatter: v => 'RM ' + v.toLocaleString('ms-MY'), style: { fontSize: '11px' } } },
    yaxis: { labels: { style: { fontSize: '11px' } } },
    grid: { borderColor: '#f1f5f9' }
}).render();
@else
document.querySelector('#chart-company').innerHTML = '<p class="text-center text-gray-400 text-sm py-12">{{ __('messages.no_data') }}</p>';
@endif
</script>
@endpush
@endsection
