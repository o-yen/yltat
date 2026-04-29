@extends('layouts.admin')
@section('title', __('nav.feedback'))
@section('page-title', __('nav.feedback'))

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ __('common.total_feedbacks', ['count' => $feedbacks->total()]) }}</p>
    @if($showGenericCreateButton)
        <a href="{{ route('admin.feedback.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('common.add_feedback') }}
        </a>
    @endif
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <select name="feedback_from" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <option value="">{{ __('common.all_sources') }}</option>
            <option value="company" {{ request('feedback_from') === 'company' ? 'selected' : '' }}>{{ __('common.feedback_from.company') }}</option>
            <option value="talent" {{ request('feedback_from') === 'talent' ? 'selected' : '' }}>{{ __('common.feedback_from.talent') }}</option>
            <option value="yltat" {{ request('feedback_from') === 'yltat' ? 'selected' : '' }}>{{ __('common.feedback_from.yltat') }}</option>
        </select>
        <div class="relative min-w-64" x-data="fbPlacementFilter()" @click.away="showDropdown = false">
            <input type="text" x-model="searchQuery" @input="filterList()" @focus="filterList(); showDropdown = true"
                   placeholder="{{ __('messages.select_placement') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <div x-show="selectedId" class="mt-1 inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs">
                <span x-text="selectedLabel" class="truncate max-w-48"></span>
                <button type="button" @click="clearSelection()" class="text-blue-400 hover:text-blue-600">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div x-show="showDropdown && filtered.length > 0"
                 class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-52 overflow-y-auto">
                <button type="button" @click="clearSelection(); showDropdown = false"
                        class="w-full text-left px-3 py-2 text-sm text-gray-400 hover:bg-gray-50 border-b border-gray-100">
                    {{ __('messages.select_placement') }}
                </button>
                <template x-for="item in filtered" :key="item.id">
                    <button type="button" @click="selectItem(item)"
                            class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-50 truncate" x-text="item.label"></button>
                </template>
            </div>
            <input type="hidden" name="placement_id" :value="selectedId">
        </div>
        <button type="submit" class="px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium">{{ __('messages.filter') }}</button>
        @if(request()->hasAny(['feedback_from', 'placement_id']))
            <a href="{{ route('admin.feedback.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm">{{ __('common.reset') }}</a>
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
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.source') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.average_score') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">{{ __('common.date') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($feedbacks as $fb)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $fb->placement?->talent?->full_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $fb->placement?->company?->company_name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $colors = ['company' => 'bg-blue-100 text-blue-700', 'talent' => 'bg-green-100 text-green-700', 'yltat' => 'bg-purple-100 text-purple-700'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$fb->feedback_from] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ __('common.feedback_from.' . $fb->feedback_from, [], null) ?: $fb->feedback_from }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($fb->average_score)
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-sm font-bold
                                    {{ $fb->average_score >= 4 ? 'bg-green-100 text-green-700' : ($fb->average_score >= 3 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ number_format($fb->average_score, 1) }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs hidden md:table-cell">{{ $fb->submitted_at?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.feedback.show', $fb) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg inline-block">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">{{ __('messages.no_feedback_found') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($feedbacks->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $feedbacks->links() }}</div>
    @endif
</div>
@endsection

@php
    $fbPlacementsJson = $placements->map(function($p) {
        $talent = $p->talent?->full_name ?? '-';
        $company = $p->company?->company_name ?? '-';
        return ['id' => $p->id, 'label' => $talent . ' — ' . $company];
    })->values();
@endphp
@push('scripts')
<script>
function fbPlacementFilter() {
    const allPlacements = @json($fbPlacementsJson);
    const currentId = '{{ request("placement_id", "") }}';
    const found = allPlacements.find(p => String(p.id) === currentId);
    return {
        searchQuery: found ? found.label : '',
        showDropdown: false,
        selectedId: currentId,
        selectedLabel: found ? found.label : '',
        filtered: [],
        filterList() {
            const q = (this.searchQuery || '').toLowerCase();
            this.filtered = allPlacements.filter(p => !q || p.label.toLowerCase().includes(q)).slice(0, 20);
            this.showDropdown = true;
        },
        selectItem(item) { this.selectedId = item.id; this.selectedLabel = item.label; this.searchQuery = item.label; this.showDropdown = false; },
        clearSelection() { this.selectedId = ''; this.selectedLabel = ''; this.searchQuery = ''; }
    };
}
</script>
@endpush
