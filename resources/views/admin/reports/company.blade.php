@extends('layouts.admin')
@section('title', __('common.company_report'))
@section('page-title', __('common.company_report') . ' ' . $currentYear)

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
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.code') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.company') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.industry') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.placements') }}</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.allocated_rm') }}</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.spent_rm') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.agreement') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($companies as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-blue-700">{{ $c->company_code }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $c->company_name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $c->industry ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $c->placements_count }}</td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($c->budget_allocated, 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono {{ $c->budget_used > $c->budget_allocated ? 'text-red-700 font-bold' : 'text-gray-800' }}">{{ number_format($c->budget_used, 2) }}</td>
                        <td class="px-4 py-3">@include('partials.talent-status-badge', ['status' => $c->agreement_status])</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">{{ __('messages.no_records') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
