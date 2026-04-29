@extends('layouts.admin')
@section('title', __('common.budget_report'))
@section('page-title', __('common.budget_report') . ' ' . $currentYear)

@section('content')
<div class="flex items-center justify-between mb-5">
    <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ __('messages.back') }}
    </a>
    <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
        {{ __('common.export_csv') }}
    </a>
</div>

<!-- Summary -->
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="text-xs text-gray-500 uppercase font-semibold mb-1">{{ __('common.total_allocation') }}</div>
        <div class="text-2xl font-bold text-gray-800">RM {{ number_format($totalAllocated, 2) }}</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="text-xs text-gray-500 uppercase font-semibold mb-1">{{ __('common.total_spent') }}</div>
        <div class="text-2xl font-bold text-red-700">RM {{ number_format($totalSpent, 2) }}</div>
    </div>
</div>

<!-- By Category -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">{{ __('common.expenses_by_category') }}</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        @foreach($byCategory as $cat)
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">{{ ucfirst($cat->category) }}</div>
                <div class="text-lg font-bold text-gray-800">RM {{ number_format($cat->total, 2) }}</div>
            </div>
        @endforeach
    </div>
</div>

<!-- Transactions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">{{ __('common.transaction_list') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.date') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.category') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.company_talent') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.description') }}</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.amount_rm') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($transactions as $txn)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600">{{ $txn->transaction_date?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3"><span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs">{{ ucfirst($txn->category) }}</span></td>
                        <td class="px-4 py-3 text-gray-600">{{ $txn->company?->company_name ?? $txn->talent?->full_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $txn->description ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-mono font-semibold">{{ number_format($txn->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">{{ __('messages.no_transactions') }}</td></tr>
                @endforelse
            </tbody>
            @if($transactions->count() > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-4 py-3 font-semibold text-gray-800">{{ __('common.grand_total') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800">RM {{ number_format($transactions->sum('amount'), 2) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
