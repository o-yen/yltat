@extends('layouts.admin')
@section('title', __('common.placement_info'))
@section('page-title', __('common.placement_info'))

@section('content')
<div class="flex items-center justify-between mb-5">
    <a href="{{ route('admin.placements.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ __('common.back_to_list') }}
    </a>
    <a href="{{ route('admin.placements.edit', $placement) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        {{ __('common.edit_placement') }}
    </a>
</div>

<!-- Header -->
<div class="bg-gradient-to-r from-[#1E3A5F] to-[#2d5a8e] rounded-xl p-6 text-white mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <p class="text-blue-200 text-xs uppercase font-semibold mb-1">{{ __('common.talent') }}</p>
            <a href="{{ route('admin.talents.show', $placement->talent_id) }}" class="text-white font-bold text-lg hover:underline">
                {{ $placement->talent?->full_name }}
            </a>
            <p class="text-blue-200 text-sm">{{ $placement->talent?->talent_code }}</p>
        </div>
        <div>
            <p class="text-blue-200 text-xs uppercase font-semibold mb-1">{{ __('common.company_name_short') }}</p>
            @if($placement->company?->company_code && str_starts_with($placement->company->company_code, 'SPTAN_'))
                <a href="{{ route('admin.syarikat-penempatan.show', $placement->company->company_code) }}" class="text-white font-bold text-lg hover:underline">
                    {{ $placement->company->company_name }}
                </a>
            @else
                <span class="text-white font-bold text-lg">{{ $placement->company?->company_name ?? '-' }}</span>
            @endif
            @if($placement->department)
                <p class="text-blue-200 text-sm">{{ $placement->department }}</p>
            @endif
        </div>
        <div class="text-right">
            @include('partials.talent-status-badge', ['status' => $placement->placement_status])
            <p class="text-blue-200 text-sm mt-2">
                {{ $placement->start_date?->format('d/m/Y') }} - {{ $placement->end_date?->format('d/m/Y') }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('common.placement_info') }}</h3>
        <div class="space-y-3 text-sm">
            @foreach([
                [__('common.intake_batch'), $placement->batch?->batch_name],
                [__('common.supervisor_name'), $placement->supervisor_name],
                [__('common.supervisor_email'), $placement->supervisor_email],
                [__('common.duration_months_label'), $placement->duration_months ? $placement->duration_months . ' ' . __('common.months') : null],
                [__('common.programme_type'), $placement->programme_type],
            ] as [$label, $value])
                @if($value)
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase">{{ $label }}</span>
                        <p class="text-gray-800 mt-0.5">{{ $value }}</p>
                    </div>
                @endif
            @endforeach

            <div class="border-t border-gray-100 pt-3 mt-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-blue-50 rounded-lg p-3 text-center">
                        <div class="text-lg font-bold text-blue-800">RM {{ number_format($placement->monthly_stipend, 2) }}</div>
                        <div class="text-xs text-blue-600">{{ __('common.monthly_allowance') }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <div class="text-lg font-bold text-gray-800">RM {{ number_format($placement->additional_cost, 2) }}</div>
                        <div class="text-xs text-gray-600">{{ __('common.additional_cost') }}</div>
                    </div>
                </div>
            </div>

            @if($placement->remarks)
                <div class="border-t border-gray-100 pt-3">
                    <span class="text-xs font-semibold text-gray-400 uppercase">{{ __('common.remarks') }}</span>
                    <p class="text-gray-700 mt-0.5">{{ $placement->remarks }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Feedback & Transactions -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Feedback -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">{{ __('nav.feedback') }}</h3>
                <a href="{{ route('admin.feedback.create', ['placement_id' => $placement->id]) }}"
                   class="text-sm text-blue-600 hover:text-blue-800 font-medium">{{ __('common.add_feedback') }}</a>
            </div>
            @forelse($placement->feedback as $fb)
                <div class="p-4 bg-gray-50 rounded-lg mb-3">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-sm font-semibold text-gray-700">{{ ucfirst($fb->feedback_from) }}</span>
                        <div class="flex gap-2">
                            @foreach([
                                $fb->score_technical, $fb->score_communication,
                                $fb->score_discipline, $fb->score_problem_solving, $fb->score_professionalism
                            ] as $score)
                                @if($score)
                                    <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                        {{ $score >= 4 ? 'bg-green-100 text-green-700' : ($score >= 3 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ $score }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @if($fb->comments)
                        <p class="text-sm text-gray-600">{{ $fb->comments }}</p>
                    @endif
                </div>
            @empty
                <p class="text-gray-400 text-sm text-center py-4">{{ __('messages.no_feedback_yet') }}</p>
            @endforelse
        </div>

        <!-- Transactions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">{{ __('common.budget_transactions') }}</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.date') }}</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.category') }}</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.amount_rm') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($placement->budgetTransactions as $txn)
                        <tr>
                            <td class="px-4 py-3 text-gray-600">{{ $txn->transaction_date?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3"><span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs">{{ ucfirst($txn->category) }}</span></td>
                            <td class="px-4 py-3 text-right font-mono font-medium">{{ number_format($txn->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-4 text-center text-gray-400 text-sm">{{ __('messages.no_transactions') }}</td></tr>
                    @endforelse
                    @if($placement->budgetTransactions->count() > 0)
                        <tr class="bg-gray-50">
                            <td class="px-4 py-3 font-semibold text-gray-800" colspan="2">{{ __('common.total') }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-800">RM {{ number_format($placement->budgetTransactions->sum('amount'), 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
