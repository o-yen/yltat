@extends('layouts.company')
@section('title', __('company.placements_title'))
@section('page-title', __('company.placements_title'))

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">{{ __('company.placements_list_title') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('company.placements_list_desc', ['company' => $company->company_name]) }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('messages.all_status') }}</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('common.status.active') }}</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>{{ __('common.status.confirmed') }}</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('common.status.completed') }}</option>
                <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>{{ __('common.status.planned') }}</option>
            </select>
            <button type="submit" class="bg-[#1E3A5F] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#274670] transition-colors">{{ __('messages.filter') }}</button>
            @if(request('status'))
                <a href="{{ route('company.placements.index') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('messages.cancel') }}</a>
            @endif
        </form>
    </div>

    {{-- List --}}
    <div class="space-y-3">
        @forelse($placements as $placement)
            @php
                $statusColor = match($placement->placement_status) {
                    'active','confirmed' => 'bg-green-100 text-green-700',
                    'completed' => 'bg-blue-100 text-blue-700',
                    'planned' => 'bg-yellow-100 text-yellow-700',
                    'terminated' => 'bg-red-100 text-red-700',
                    default => 'bg-gray-100 text-gray-600',
                };
                $hasFeedback = $placement->feedback->where('feedback_from','company')->count() > 0;
            @endphp
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:border-[#1E3A5F]/20 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-[#1E3A5F] flex items-center justify-center text-white text-lg font-bold flex-shrink-0">
                        {{ substr($placement->talent?->full_name ?? '?', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-gray-800">{{ $placement->talent?->full_name ?? '—' }}</p>
                            <span class="text-xs font-mono text-gray-400">{{ $placement->talent?->talent_code }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColor }}">{{ __('common.status.' . $placement->placement_status) }}</span>
                        </div>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1">
                            <p class="text-xs text-gray-500">{{ $placement->department ?? __('company.no_department') }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $placement->start_date?->format('d M Y') }} — {{ $placement->end_date?->format('d M Y') }}
                            </p>
                            <p class="text-xs text-gray-500">RM {{ number_format($placement->monthly_stipend, 2) }}/{{ __('common.month') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @if(! $hasFeedback && in_array($placement->placement_status, ['active','confirmed']))
                            <a href="{{ route('company.feedback.create', ['placement_id' => $placement->id]) }}"
                               class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg hover:bg-amber-100 transition-colors whitespace-nowrap">
                                + {{ __('nav.feedback') }}
                            </a>
                        @elseif($hasFeedback)
                            <span class="text-xs bg-green-50 text-green-700 border border-green-200 px-3 py-1.5 rounded-lg whitespace-nowrap">✓ {{ __('common.ratings') }}</span>
                        @endif
                        <a href="{{ route('company.placements.show', $placement) }}"
                           class="text-xs bg-[#1E3A5F] text-white px-3 py-1.5 rounded-lg hover:bg-[#274670] transition-colors whitespace-nowrap">
                            {{ __('messages.view') }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
                <p class="text-gray-400 font-medium">{{ __('company.no_placement_records') }}</p>
            </div>
        @endforelse
    </div>

    @if($placements->hasPages())
        <div>{{ $placements->links() }}</div>
    @endif
</div>
@endsection
