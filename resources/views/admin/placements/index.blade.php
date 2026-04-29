@extends('layouts.admin')
@section('title', __('nav.placements'))
@section('page-title', __('nav.placements'))

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <p class="text-sm text-gray-500">{{ __('common.total_placements', ['count' => $placements->total()]) }}</p>
    <a href="{{ route('admin.placements.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        {{ __('common.add_placement') }}
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-56">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('common.search_talent_company') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
        </div>
        <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <option value="">{{ __('common.all_statuses') }}</option>
            @foreach($statuses as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ __('common.status.' . $s) }}</option>
            @endforeach
        </select>
        <select name="company_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <option value="">{{ __('common.all_companies') }}</option>
            @foreach($companies as $c)
                <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
            @endforeach
        </select>
        <select name="batch_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <option value="">{{ __('common.all_batches') }}</option>
            @foreach($batches as $b)
                <option value="{{ $b->id }}" {{ request('batch_id') == $b->id ? 'selected' : '' }}>{{ $b->batch_name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium">{{ __('messages.filter') }}</button>
        @if(request()->hasAny(['search', 'status', 'company_id', 'batch_id']))
            <a href="{{ route('admin.placements.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm">{{ __('common.reset') }}</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.talent') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.company_name_short') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">{{ __('common.department') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">{{ __('common.period') }}</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">{{ __('common.monthly_allowance') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.status_label') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($placements as $placement)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.talents.show', $placement->talent_id) }}" class="font-medium text-blue-700 hover:underline">
                                {{ $placement->talent?->full_name ?? '-' }}
                            </a>
                            @if($placement->batch)
                                <div class="text-xs text-gray-400">{{ $placement->batch->batch_name }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($placement->company?->company_code && str_starts_with($placement->company->company_code, 'SPTAN_'))
                                <a href="{{ route('admin.syarikat-penempatan.show', $placement->company->company_code) }}" class="text-gray-700 hover:text-blue-700">
                                    {{ $placement->company->company_name }}
                                </a>
                            @else
                                <span class="text-gray-700">{{ $placement->company?->company_name ?? '-' }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $placement->department ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs hidden lg:table-cell">
                            {{ $placement->start_date?->format('d/m/Y') }} -<br>{{ $placement->end_date?->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-right font-medium hidden lg:table-cell">RM {{ number_format($placement->monthly_stipend, 2) }}</td>
                        <td class="px-4 py-3">@include('partials.talent-status-badge', ['status' => $placement->placement_status])</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.placements.show', $placement) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="{{ __('messages.view') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.placements.edit', $placement) }}" class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg" title="{{ __('messages.edit') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">{{ __('common.no_placements_found') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($placements->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $placements->links() }}</div>
    @endif
</div>
@endsection
