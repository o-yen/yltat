@extends('layouts.admin')

@section('title', __('protege.kw_title') . ' - ' . $kewangan->id_graduan)
@section('page-title', __('protege.kw_show_title'))

@section('content')
<div class="mb-5 flex items-center justify-between">
    <a href="{{ route('admin.kewangan.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('protege.back_to_list') }}
    </a>
    <a href="{{ route('admin.kewangan.edit', $kewangan) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        {{ __('protege.kemaskini') }}
    </a>
</div>

<!-- Header Card -->
<div class="bg-gradient-to-r from-[#1E3A5F] to-[#2d5a8e] rounded-xl p-6 text-white mb-6 shadow-md">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold">{{ $kewangan->id_graduan }}</h2>
            <p class="text-blue-200 mt-1">{{ $kewangan->bulan }} {{ $kewangan->tahun }}</p>
            @if($kewangan->syarikatPelaksana)
                <p class="text-blue-200 text-sm mt-1">{{ $kewangan->syarikatPelaksana->nama_syarikat }}</p>
            @endif
        </div>
        <div class="text-right">
            <div class="text-3xl font-bold">RM {{ number_format($kewangan->elaun_prorate, 2) }}</div>
            <div class="text-xs text-blue-200 mt-1">{{ __('protege.kw_elaun_prorate_label') }}</div>
            @php
                $statusColors = [
                    'Selesai' => 'bg-green-500/20 text-green-100',
                    'Dalam Proses' => 'bg-blue-500/20 text-blue-100',
                    'Lewat' => 'bg-red-500/20 text-red-100',
                ];
                $statusLabels = [
                    'Selesai' => __('protege.bayaran_selesai'),
                    'Dalam Proses' => __('protege.bayaran_dalam_proses'),
                    'Lewat' => __('protege.bayaran_lewat'),
                ];
            @endphp
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold mt-2 {{ $statusColors[$kewangan->status_bayaran] ?? 'bg-white/10' }}">
                {{ $statusLabels[$kewangan->status_bayaran] ?? $kewangan->status_bayaran }}
            </span>
        </div>
    </div>
</div>

<!-- Detail Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.kw_info') }}</h3>
        <div class="space-y-4">
            @foreach([
                [__('protege.id_graduan'), $kewangan->id_graduan],
                [__('protege.kh_bulan_tahun'), $kewangan->bulan . ' ' . $kewangan->tahun],
                [__('protege.kw_elaun_penuh'), 'RM ' . number_format($kewangan->elaun_penuh, 2)],
                [__('protege.kw_elaun_prorate_label'), 'RM ' . number_format($kewangan->elaun_prorate, 2)],
                [__('protege.kw_hari_bekerja'), $kewangan->hari_bekerja_sebenar],
                [__('protege.kw_hari_dalam_bulan'), $kewangan->hari_dalam_bulan],
            ] as [$label, $value])
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</label>
                    <p class="text-gray-800 mt-1">{{ $value ?? '-' }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.kw_bayaran_info') }}</h3>
        <div class="space-y-4">
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.kw_status_bayaran') }}</label>
                <div class="mt-1">
                    @php
                        $badgeColors = [
                            'Selesai' => 'bg-green-100 text-green-700',
                            'Dalam Proses' => 'bg-blue-100 text-blue-700',
                            'Lewat' => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColors[$kewangan->status_bayaran] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $statusLabels[$kewangan->status_bayaran] ?? $kewangan->status_bayaran }}
                    </span>
                </div>
            </div>

            @foreach([
                [__('protege.kw_tarikh_mula'), $kewangan->tarikh_mula_kerja ? \Carbon\Carbon::parse($kewangan->tarikh_mula_kerja)->format('d/m/Y') : null],
                [__('protege.kw_tarikh_akhir'), $kewangan->tarikh_akhir_kerja ? \Carbon\Carbon::parse($kewangan->tarikh_akhir_kerja)->format('d/m/Y') : null],
                [__('protege.kw_tarikh_bayar'), $kewangan->tarikh_bayar ? \Carbon\Carbon::parse($kewangan->tarikh_bayar)->format('d/m/Y') : null],
                [__('protege.kw_tarikh_jangka'), $kewangan->tarikh_jangka_bayar ? \Carbon\Carbon::parse($kewangan->tarikh_jangka_bayar)->format('d/m/Y') : null],
            ] as [$label, $value])
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</label>
                    <p class="text-gray-800 mt-1">{{ $value ?? '-' }}</p>
                </div>
            @endforeach

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.kw_hari_lewat') }}</label>
                <p class="mt-1 {{ ($kewangan->hari_lewat ?? 0) > 7 ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                    {{ ($kewangan->hari_lewat ?? 0) > 0 ? $kewangan->hari_lewat . ' ' . __('protege.kw_hari') : '-' }}
                </p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.pelaksana') }}</label>
                <p class="text-gray-800 mt-1">{{ $kewangan->syarikatPelaksana->nama_syarikat ?? '-' }}</p>
            </div>

            @if($kewangan->catatan)
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.catatan') }}</label>
                    <div class="mt-1 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $kewangan->catatan }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
