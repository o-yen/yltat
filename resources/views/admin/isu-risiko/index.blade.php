@extends('layouts.admin')

@section('title', __('protege.isu_title'))
@section('page-title', __('protege.isu_title'))

@section('content')
<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.isu_baru') }}</p>
                <p class="text-xl font-bold text-blue-600">{{ $totalBaru }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.isu_dalam_tindakan') }}</p>
                <p class="text-xl font-bold text-yellow-600">{{ $totalDalamTindakan }}</p>
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
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.isu_kritikal_aktif') }}</p>
                <p class="text-xl font-bold text-red-600">{{ $totalKritikal }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-gray-500">{{ __('protege.total_records', ['count' => $records->total()]) }}</p>
    </div>
    <a href="{{ route('admin.isu-risiko.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('protege.isu_add') }}
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('admin.isu-risiko.index') }}" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('protege.isu_search') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
        </div>
        <div class="min-w-40">
            <select name="kategori_isu" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.all') }}</option>
                @foreach(['Bayaran Lewat' => __('protege.kat_bayaran_lewat'), 'Kehadiran Rendah' => __('protege.kat_kehadiran_rendah'), 'Prestasi Lemah' => __('protege.kat_prestasi_lemah'), 'Logbook Lewat' => __('protege.kat_logbook_lewat'), 'Isu Pematuhan' => __('protege.kat_pematuhan'), 'Masalah Komunikasi' => __('protege.kat_komunikasi'), 'Lain-lain' => __('protege.kat_lain')] as $ki => $label)
                    <option value="{{ $ki }}" {{ request('kategori_isu') === $ki ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-36">
            <select name="tahap_risiko" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.all') }}</option>
                @foreach(['Kritikal' => __('protege.risiko_kritikal'), 'Tinggi' => __('protege.risiko_tinggi'), 'Sederhana' => __('protege.risiko_sederhana'), 'Rendah' => __('protege.risiko_rendah')] as $tr => $label)
                    <option value="{{ $tr }}" {{ request('tahap_risiko') === $tr ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-36">
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.all_statuses') }}</option>
                @foreach(['Baru' => __('protege.isu_status_baru'), 'Dalam Tindakan' => __('protege.isu_status_dalam'), 'Selesai' => __('protege.isu_status_selesai'), 'Ditutup' => __('protege.isu_status_ditutup')] as $st => $label)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
            {{ __('protege.filter') }}
        </button>
        @if(request()->hasAny(['search', 'kategori_isu', 'tahap_risiko', 'status']))
            <a href="{{ route('admin.isu-risiko.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
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
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.isu_id') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">{{ __('protege.isu_tarikh') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.isu_kategori') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.isu_butiran') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.isu_tahap_risiko') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.status') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">{{ __('protege.isu_pic') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.tindakan') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($records as $record)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded">{{ $record->id_isu }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $record->tarikh_isu ? \Carbon\Carbon::parse($record->tarikh_isu)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">{{ $record->kategori_isu }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ Str::limit($record->butiran_isu, 60) }}</td>
                        <td class="px-4 py-3">
                            @php
                                $risikoColors = [
                                    'Kritikal' => 'bg-red-100 text-red-700',
                                    'Tinggi' => 'bg-orange-100 text-orange-700',
                                    'Sederhana' => 'bg-yellow-100 text-yellow-700',
                                    'Rendah' => 'bg-green-100 text-green-700',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $risikoColors[$record->tahap_risiko] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $record->tahap_risiko }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'Baru' => 'bg-blue-100 text-blue-700',
                                    'Dalam Tindakan' => 'bg-yellow-100 text-yellow-700',
                                    'Selesai' => 'bg-green-100 text-green-700',
                                    'Ditutup' => 'bg-gray-100 text-gray-600',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $record->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden lg:table-cell">{{ $record->pic ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.isu-risiko.show', $record) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="{{ __('protege.view') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.isu-risiko.edit', $record) }}"
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
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
