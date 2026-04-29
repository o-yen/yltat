@extends('layouts.admin')

@section('title', __('protege.kh_title'))
@section('page-title', __('protege.kh_title'))

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-gray-500">{{ __('protege.total_records', ['count' => $records->total()]) }}</p>
    </div>
    @if(in_array(\App\Http\Middleware\ModuleAccess::levelFor(auth()->user()->role?->role_name, 'kehadiran'), ['full', 'edit', 'own', 'create']))
    <a href="{{ route('admin.kehadiran.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('protege.kh_add') }}
    </a>
    @endif
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('admin.kehadiran.index') }}" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('protege.kh_search') }}"
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
        @if(empty($isCompanyRole))
        <div class="min-w-40">
            <select name="id_pelaksana" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.all_pelaksana') }}</option>
                @foreach($pelaksana as $p)
                    <option value="{{ $p->id_pelaksana }}" {{ request('id_pelaksana') == $p->id_pelaksana ? 'selected' : '' }}>{{ $p->nama_syarikat }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-40">
            <select name="id_syarikat" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.all_penempatan') }}</option>
                @foreach($penempatan as $s)
                    <option value="{{ $s->id_syarikat }}" {{ request('id_syarikat') == $s->id_syarikat ? 'selected' : '' }}>{{ $s->nama_syarikat }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <button type="submit" class="px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
            {{ __('protege.filter') }}
        </button>
        @if(request()->hasAny(['search', 'bulan', 'id_pelaksana', 'id_syarikat']))
            <a href="{{ route('admin.kehadiran.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
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
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.kh_kehadiran_pct') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">{{ __('protege.kh_hari_hadir') }}/{{ __('protege.kh_hari_bekerja') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.kh_skor_prestasi') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">{{ __('protege.kh_status_logbook') }}</th>
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
                        <td class="px-4 py-3 text-center">
                            @php $pct = $record->kehadiran_pct ?? 0; @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $pct >= 0.85 ? 'bg-green-100 text-green-700' : ($pct >= 0.75 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ number_format($pct * 100, 0) }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 hidden md:table-cell">{{ $record->hari_hadir }}/{{ $record->hari_bekerja }}</td>
                        <td class="px-4 py-3 text-center">
                            @php $skor = $record->skor_prestasi ?? 0; @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $skor >= 8 ? 'bg-green-100 text-green-700' : ($skor >= 6 ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700') }}">
                                {{ $skor }}/10
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @php
                                $logbookColors = [
                                    'Dikemukakan' => 'bg-green-100 text-green-700',
                                    'Lewat' => 'bg-yellow-100 text-yellow-700',
                                    'Belum Dikemukakan' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            @php
                                $logbookLabels = [
                                    'Dikemukakan' => __('protege.lb_dikemukakan'),
                                    'Lewat' => __('protege.lb_lewat'),
                                    'Belum Dikemukakan' => __('protege.lb_belum'),
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $logbookColors[$record->status_logbook] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $logbookLabels[$record->status_logbook] ?? $record->status_logbook }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.kehadiran.show', $record) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="{{ __('protege.view') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.kehadiran.edit', $record) }}"
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
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
