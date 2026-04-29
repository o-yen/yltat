@extends('layouts.admin')

@section('title', __('protege.kw_title'))
@section('page-title', __('protege.kw_title'))

@section('content')
<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.kw_total_dibayar') }}</p>
                <p class="text-xl font-bold text-gray-800">RM {{ number_format($totalPaid, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.kw_dalam_proses') }}</p>
                <p class="text-xl font-bold text-gray-800">RM {{ number_format($totalPending, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.kw_lewat') }}</p>
                <p class="text-xl font-bold text-red-600">{{ $totalLate }} {{ __('protege.kw_rekod') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-gray-500">{{ __('protege.total_records', ['count' => $records->total()]) }}</p>
    </div>
    <a href="{{ route('admin.kewangan.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('protege.kw_add') }}
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('admin.kewangan.index') }}" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('protege.kw_search') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
        </div>
        <div class="min-w-36">
            <select name="bulan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.all_months') }}</option>
                @foreach($bulanList as $bulan)
                    <option value="{{ $bulan }}" {{ request('bulan') === $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-40">
            <select name="id_pelaksana" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.all_pelaksana') }}</option>
                @foreach($pelaksana as $p)
                    <option value="{{ $p->id_pelaksana }}" {{ request('id_pelaksana') == $p->id_pelaksana ? 'selected' : '' }}>{{ $p->nama_syarikat }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-36">
            <select name="status_bayaran" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.all_statuses') }}</option>
                @foreach(['Selesai' => __('protege.bayaran_selesai'), 'Dalam Proses' => __('protege.bayaran_dalam_proses'), 'Lewat' => __('protege.bayaran_lewat')] as $val => $label)
                    <option value="{{ $val }}" {{ request('status_bayaran') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
            {{ __('protege.filter') }}
        </button>
        @if(request()->hasAny(['search', 'bulan', 'id_pelaksana', 'status_bayaran']))
            <a href="{{ route('admin.kewangan.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                {{ __('protege.reset') }}
            </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.id_graduan') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.bulan') }}</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.kw_elaun_penuh') }}</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.kw_elaun_prorate') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.kw_status_bayaran') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">{{ __('protege.kw_hari_lewat') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.tindakan') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($records as $record)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded">{{ $record->id_graduan }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $record->bulan }} {{ $record->tahun }}</td>
                        <td class="px-4 py-3 text-right font-mono text-gray-800">RM {{ number_format($record->elaun_penuh, 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-medium text-gray-800">RM {{ number_format($record->elaun_prorate, 2) }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'Selesai' => 'bg-green-100 text-green-700',
                                    'Dalam Proses' => 'bg-blue-100 text-blue-700',
                                    'Lewat' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            @php
                                $statusLabels = [
                                    'Selesai' => __('protege.bayaran_selesai'),
                                    'Dalam Proses' => __('protege.bayaran_dalam_proses'),
                                    'Lewat' => __('protege.bayaran_lewat'),
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$record->status_bayaran] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $statusLabels[$record->status_bayaran] ?? $record->status_bayaran }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center hidden md:table-cell">
                            @if(($record->hari_lewat ?? 0) > 7)
                                <span class="text-red-600 font-semibold">{{ $record->hari_lewat }} {{ __('protege.kw_hari') }}</span>
                            @elseif(($record->hari_lewat ?? 0) > 0)
                                <span class="text-yellow-600">{{ $record->hari_lewat }} {{ __('protege.kw_hari') }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.kewangan.show', $record) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="{{ __('protege.view') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.kewangan.edit', $record) }}"
                                   class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="{{ __('protege.kemaskini') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p>{{ __('protege.no_records') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($records->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $records->links() }}
        </div>
    @endif
</div>
@endsection
