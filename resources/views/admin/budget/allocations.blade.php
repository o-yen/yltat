@extends('layouts.admin')
@section('title', __('common.allocation_list'))
@section('page-title', __('common.allocation_list'))

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.budget.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ __('common.back_to_dashboard') }}
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Add Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('common.add_allocation') }}</h3>
        <form method="POST" action="{{ route('admin.budget.allocations.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.fiscal_year') }} *</label>
                    <select name="fiscal_year" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        @foreach($fiscalYears as $yr)
                            <option value="{{ $yr }}" {{ old('fiscal_year', date('Y')) == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.pelaksana') }}</label>
                    <select name="id_pelaksana" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('common.general_overall') }}</option>
                        @foreach($pelaksana as $p)
                            <option value="{{ $p->id_pelaksana }}">{{ $p->nama_syarikat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.batch_optional') }}</label>
                    <select name="batch_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('common.none_label') }}</option>
                        @foreach($batches as $b)
                            <option value="{{ $b->id }}">{{ $b->batch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.allocation_amount_rm') }} *</label>
                    <input type="number" name="allocated_amount" value="{{ old('allocated_amount') }}" min="0" step="0.01" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.notes') }}</label>
                    <textarea name="remarks" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"></textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors">{{ __('common.save_allocation') }}</button>
            </div>
        </form>
    </div>

    <!-- List -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">{{ __('common.allocation_list') }}</h3>
            <form method="GET">
                <select name="fiscal_year" onchange="this.form.submit()"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                    @foreach([date('Y'), date('Y') - 1, date('Y') + 1] as $yr)
                        <option value="{{ $yr }}" {{ request('fiscal_year', date('Y')) == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.year') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.company_batch') }}</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.amount_rm') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($allocations as $alloc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600">{{ $alloc->fiscal_year }}</td>
                        <td class="px-4 py-3">
                            <div class="text-gray-800">{{ $alloc->company?->company_name ?? '-' }}</div>
                            @if($alloc->batch)
                                <div class="text-xs text-gray-400">{{ $alloc->batch->batch_name }}</div>
                            @endif
                            @if($alloc->remarks)
                                <div class="text-xs text-gray-400 italic">{{ $alloc->remarks }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ number_format($alloc->allocated_amount, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.budget.allocations.destroy', $alloc) }}"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete_allocation') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">{{ __('messages.no_allocations') }}</td></tr>
                @endforelse
            </tbody>
            @if($allocations->count() > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="2" class="px-4 py-3 font-semibold text-gray-800">{{ __('common.total') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800">RM {{ number_format($allocations->sum('allocated_amount'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
        @if($allocations->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $allocations->links() }}</div>
        @endif
    </div>
</div>
@endsection
