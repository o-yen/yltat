@extends('layouts.admin')
@section('title', __('common.transaction_list'))
@section('page-title', __('common.transaction_list'))

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.budget.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ __('common.back_to_dashboard') }}
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Add Form (non-allowance expenses only) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ $editTransaction ? __('common.edit_transaction') : __('common.add_transaction') }}</h3>
        <p class="text-xs text-gray-400 mb-4">{{ __('common.budget_categories.allowance') }} → <a href="{{ route('admin.kewangan.index') }}" class="text-blue-600 hover:underline">{{ __('protege.kw_title') }}</a></p>
        <form method="POST" action="{{ $editTransaction ? route('admin.budget.transactions.update', $editTransaction) : route('admin.budget.transactions.store') }}">
            @csrf
            @if($editTransaction)
                @method('PUT')
            @endif
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.date') }} *</label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', $editTransaction?->transaction_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div x-data="placementSelector()">
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.placement') }}</label>
                    <div class="relative">
                        <input type="text" x-model="searchQuery" @input="filterList()" @focus="filterList(); showDropdown = true"
                               placeholder="{{ __('messages.search') }}..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <div x-show="selectedId" class="mt-1.5 inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg text-xs">
                            <span x-text="selectedLabel" class="truncate"></span>
                            <button type="button" @click="clearSelection()" class="text-blue-400 hover:text-blue-600 flex-shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div x-show="showDropdown && filtered.length > 0" @click.away="showDropdown = false"
                             class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            <button type="button" @click="clearSelection(); showDropdown = false"
                                    class="w-full text-left px-3 py-2 text-sm text-gray-400 hover:bg-gray-50 border-b border-gray-100">
                                {{ __('common.none_label') }}
                            </button>
                            <template x-for="item in filtered" :key="item.id">
                                <button type="button" @click="selectItem(item)"
                                        class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-50">
                                    <span class="text-gray-800" x-text="item.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <input type="hidden" name="placement_id" :value="selectedId">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('protege.pelaksana') }}</label>
                    <select name="id_pelaksana" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('messages.select') }}</option>
                        @foreach($pelaksana as $p)
                            <option value="{{ $p->id_pelaksana }}" {{ old('id_pelaksana', optional($pelaksana->firstWhere('id_pelaksana', $editTransaction?->company?->company_code ?? ''))?->id_pelaksana) == $p->id_pelaksana ? 'selected' : '' }}>{{ $p->nama_syarikat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.category') }} *</label>
                    <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        @foreach(['equipment', 'training', 'admin', 'travel', 'other'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $editTransaction?->category) === $cat ? 'selected' : '' }}>{{ __('common.budget_categories.' . $cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.description') }}</label>
                    <input type="text" name="description" value="{{ old('description', $editTransaction?->description) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.amount_rm') }} *</label>
                    <input type="number" name="amount" value="{{ old('amount', $editTransaction?->amount) }}" min="0" step="0.01" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.reference_no') }}</label>
                    <input type="text" name="reference_no" value="{{ old('reference_no', $editTransaction?->reference_no) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.status_label') }}</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="approved" {{ old('status', $editTransaction?->status) === 'approved' ? 'selected' : '' }}>{{ __('common.status.approved') }}</option>
                        <option value="pending" {{ old('status', $editTransaction?->status) === 'pending' ? 'selected' : '' }}>{{ __('common.status.pending') }}</option>
                        <option value="rejected" {{ old('status', $editTransaction?->status) === 'rejected' ? 'selected' : '' }}>{{ __('common.status.rejected') }}</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors">{{ $editTransaction ? __('common.update_transaction') : __('common.save_transaction') }}</button>
                @if($editTransaction)
                    <a href="{{ route('admin.budget.transactions') }}" class="block w-full py-2.5 text-center bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">{{ __('common.cancel') }}</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Merged List (budget + kewangan) -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">{{ __('common.transaction_list') }}</h3>
        </div>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search') }}..."
                       class="flex-1 min-w-36 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    <option value="">{{ __('common.all_categories') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ __('common.budget_categories.' . $cat) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm">{{ __('messages.filter') }}</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.date') }}</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.category') }}</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">{{ __('common.company_talent') }}</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.amount_rm') }}</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.status_label') }}</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($paginatedRows as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-600">{{ $row['date'] ? \Carbon\Carbon::parse($row['date'])->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="{{ $row['category'] === 'allowance' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700' }} px-2 py-0.5 rounded text-xs">
                                    {{ __('common.budget_categories.' . $row['category']) }}
                                </span>
                                @if($row['description'])
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $row['description'] }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $row['company'] }}</td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-gray-800">{{ number_format($row['amount'], 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($row['status'] === 'approved')
                                    <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded">{{ __('common.status.approved') }}</span>
                                @elseif($row['status'] === 'pending')
                                    <span class="text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded">{{ __('common.status.pending') }}</span>
                                @else
                                    <span class="text-xs text-gray-500">{{ $row['status'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($row['type'] === 'budget')
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.budget.transactions', array_merge(request()->query(), ['edit_transaction' => $row['id']])) }}" class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg" title="{{ __('common.edit') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.budget.transactions.destroy', $row['id']) }}" onsubmit="return confirm('{{ __('messages.confirm_delete_transaction') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg" title="{{ __('common.delete') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                @elseif($row['type'] === 'kewangan')
                                    <a href="{{ route('admin.kewangan.edit', $row['id']) }}" class="inline-flex items-center justify-center p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg" title="{{ __('common.edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">{{ __('messages.no_transactions') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($paginatedRows->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $paginatedRows->links() }}</div>
        @endif
    </div>
</div>
@endsection

@php
    $placementsJson = $placements->map(function($p) {
        $label = trim(($p->talent?->full_name ?? '') . ' @ ' . ($p->company?->company_name ?? ''), ' @');
        return ['id' => $p->id, 'label' => $label];
    })->filter(function($p) { return $p['label'] !== ''; })->values();
@endphp
@push('scripts')
<script>
function placementSelector() {
    const allPlacements = @json($placementsJson);

    const editId = '{{ old("placement_id", $editTransaction?->placement_id ?? "") }}';
    const found = allPlacements.find(p => String(p.id) === editId);

    return {
        searchQuery: '',
        showDropdown: false,
        selectedId: editId,
        selectedLabel: found ? found.label : '',
        filtered: [],

        filterList() {
            const q = (this.searchQuery || '').toLowerCase();
            this.filtered = allPlacements.filter(p =>
                !q || p.label.toLowerCase().includes(q)
            ).slice(0, 20);
            this.showDropdown = true;
        },

        selectItem(item) {
            this.selectedId = item.id;
            this.selectedLabel = item.label;
            this.searchQuery = item.label;
            this.showDropdown = false;
        },

        clearSelection() {
            this.selectedId = '';
            this.selectedLabel = '';
            this.searchQuery = '';
        }
    };
}
</script>
@endpush
