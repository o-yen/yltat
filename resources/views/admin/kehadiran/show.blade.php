@extends('layouts.admin')

@section('title', __('protege.kh_title') . ' - ' . $kehadiran->id_graduan)
@section('page-title', __('protege.kh_show_title'))

@section('content')
<div class="mb-5 flex items-center justify-between">
    <a href="{{ route('admin.kehadiran.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('protege.back_to_list') }}
    </a>
    <a href="{{ route('admin.kehadiran.edit', $kehadiran) }}"
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
            <h2 class="text-2xl font-bold">{{ $kehadiran->id_graduan }}</h2>
            <p class="text-blue-200 mt-1">{{ $kehadiran->bulan }} {{ $kehadiran->tahun }}</p>
        </div>
        <div class="flex gap-3">
            @php $pct = $kehadiran->kehadiran_pct ?? 0; @endphp
            <div class="text-center px-4 py-2 rounded-lg {{ $pct >= 0.85 ? 'bg-green-500/20' : ($pct >= 0.75 ? 'bg-yellow-500/20' : 'bg-red-500/20') }}">
                <div class="text-2xl font-bold">{{ number_format($pct * 100, 0) }}%</div>
                <div class="text-xs text-blue-200">{{ __('protege.kh_kehadiran_label') }}</div>
            </div>
            @php $skor = $kehadiran->skor_prestasi ?? 0; @endphp
            <div class="text-center px-4 py-2 rounded-lg {{ $skor >= 8 ? 'bg-green-500/20' : ($skor >= 6 ? 'bg-blue-500/20' : 'bg-red-500/20') }}">
                <div class="text-2xl font-bold">{{ $skor }}/10</div>
                <div class="text-xs text-blue-200">{{ __('protege.kh_prestasi_label') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Maklumat Kehadiran -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.kh_info') }}</h3>
        <div class="space-y-4">
            @foreach([
                [__('protege.id_graduan'), $kehadiran->id_graduan],
                [__('protege.kh_bulan_tahun'), $kehadiran->bulan . ' ' . $kehadiran->tahun],
                [__('protege.kh_hari_hadir'), $kehadiran->hari_hadir],
                [__('protege.kh_hari_bekerja'), $kehadiran->hari_bekerja],
                [__('protege.kh_peratus_kehadiran'), number_format(($kehadiran->kehadiran_pct ?? 0) * 100, 1) . '%'],
            ] as [$label, $value])
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</label>
                    <p class="text-gray-800 mt-1">{{ $value ?? '-' }}</p>
                </div>
            @endforeach

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.kh_tahap_kehadiran') }}</label>
                <div class="mt-1">
                    @if($pct >= 0.85)
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ __('protege.kh_baik') }}</span>
                    @elseif($pct >= 0.75)
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ __('protege.kh_perlu_perhatian') }}</span>
                    @else
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ __('protege.kh_kritikal') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Maklumat Prestasi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.kh_prestasi_info') }}</h3>
        <div class="space-y-4">
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.kh_skor_prestasi') }}</label>
                <p class="text-gray-800 mt-1">{{ $kehadiran->skor_prestasi }}/10</p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.kh_tahap_prestasi') }}</label>
                <div class="mt-1">
                    @if($skor >= 8)
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ __('protege.kh_cemerlang') }}</span>
                    @elseif($skor >= 6)
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ __('protege.kh_memuaskan') }}</span>
                    @else
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ __('protege.kh_perlu_penambahbaikan') }}</span>
                    @endif
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.kh_status_logbook') }}</label>
                <div class="mt-1">
                    @php
                        $logbookColors = [
                            'Dikemukakan' => 'bg-green-100 text-green-700',
                            'Lewat' => 'bg-yellow-100 text-yellow-700',
                            'Belum Dikemukakan' => 'bg-red-100 text-red-700',
                        ];
                        $logbookLabels = [
                            'Dikemukakan' => __('protege.lb_dikemukakan'),
                            'Lewat' => __('protege.lb_lewat'),
                            'Belum Dikemukakan' => __('protege.lb_belum'),
                        ];
                    @endphp
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $logbookColors[$kehadiran->status_logbook] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $logbookLabels[$kehadiran->status_logbook] ?? $kehadiran->status_logbook }}
                    </span>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.pelaksana') }}</label>
                <p class="text-gray-800 mt-1">{{ $kehadiran->syarikatPelaksana->nama_syarikat ?? '-' }}</p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.penempatan') }}</label>
                <p class="text-gray-800 mt-1">{{ $kehadiran->syarikatPenempatan->nama_syarikat ?? '-' }}</p>
            </div>

            @if($kehadiran->komen_mentor)
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.kh_komen_mentor') }}</label>
                    <div class="mt-1 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $kehadiran->komen_mentor }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
