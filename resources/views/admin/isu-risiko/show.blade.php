@extends('layouts.admin')

@section('title', __('protege.isu_detail_title') . ' - ' . $isuRisiko->id_isu)
@section('page-title', __('protege.isu_detail_title'))

@section('content')
<div class="mb-5 flex items-center justify-between">
    <a href="{{ route('admin.isu-risiko.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('protege.back_to_list') }}
    </a>
    <a href="{{ route('admin.isu-risiko.edit', $isuRisiko) }}"
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
            <div class="text-blue-200 font-mono text-sm">{{ $isuRisiko->id_isu }}</div>
            <h2 class="text-xl font-bold mt-1">{{ $isuRisiko->kategori_isu }}</h2>
            <p class="text-blue-200 mt-1">{{ __('protege.isu_tarikh') }}: {{ $isuRisiko->tarikh_isu ? \Carbon\Carbon::parse($isuRisiko->tarikh_isu)->format('d/m/Y') : '-' }}</p>
        </div>
        <div class="flex gap-3">
            @php
                $risikoHeaderColors = [
                    'Kritikal' => 'bg-red-500/20 text-red-100',
                    'Tinggi' => 'bg-orange-500/20 text-orange-100',
                    'Sederhana' => 'bg-yellow-500/20 text-yellow-100',
                    'Rendah' => 'bg-green-500/20 text-green-100',
                ];
                $statusHeaderColors = [
                    'Baru' => 'bg-blue-500/20 text-blue-100',
                    'Dalam Tindakan' => 'bg-yellow-500/20 text-yellow-100',
                    'Selesai' => 'bg-green-500/20 text-green-100',
                    'Ditutup' => 'bg-white/10 text-gray-200',
                ];
            @endphp
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $risikoHeaderColors[$isuRisiko->tahap_risiko] ?? 'bg-white/10' }}">
                {{ $isuRisiko->tahap_risiko }}
            </span>
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $statusHeaderColors[$isuRisiko->status] ?? 'bg-white/10' }}">
                {{ $isuRisiko->status }}
            </span>
        </div>
    </div>
</div>

<!-- Detail Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.isu_maklumat') }}</h3>
        <div class="space-y-4">
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.isu_id') }}</label>
                <p class="text-gray-800 mt-1 font-mono">{{ $isuRisiko->id_isu }}</p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.isu_tarikh') }}</label>
                <p class="text-gray-800 mt-1">{{ $isuRisiko->tarikh_isu ? \Carbon\Carbon::parse($isuRisiko->tarikh_isu)->format('d/m/Y') : '-' }}</p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.isu_kategori') }}</label>
                <p class="text-gray-800 mt-1">
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">{{ $isuRisiko->kategori_isu }}</span>
                </p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.isu_tahap_risiko') }}</label>
                <div class="mt-1">
                    @php
                        $risikoColors = [
                            'Kritikal' => 'bg-red-100 text-red-700',
                            'Tinggi' => 'bg-orange-100 text-orange-700',
                            'Sederhana' => 'bg-yellow-100 text-yellow-700',
                            'Rendah' => 'bg-green-100 text-green-700',
                        ];
                    @endphp
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $risikoColors[$isuRisiko->tahap_risiko] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $isuRisiko->tahap_risiko }}
                    </span>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.status') }}</label>
                <div class="mt-1">
                    @php
                        $statusColors = [
                            'Baru' => 'bg-blue-100 text-blue-700',
                            'Dalam Tindakan' => 'bg-yellow-100 text-yellow-700',
                            'Selesai' => 'bg-green-100 text-green-700',
                            'Ditutup' => 'bg-gray-100 text-gray-600',
                        ];
                    @endphp
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$isuRisiko->status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $isuRisiko->status }}
                    </span>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.isu_pic') }}</label>
                <p class="text-gray-800 mt-1">{{ $isuRisiko->pic ?? '-' }}</p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.pelaksana') }}</label>
                <p class="text-gray-800 mt-1">{{ $isuRisiko->syarikatPelaksana->nama_syarikat ?? '-' }}</p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.penempatan') }}</label>
                <p class="text-gray-800 mt-1">{{ $isuRisiko->syarikatPenempatan->nama_syarikat ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.isu_butiran_tindakan') }}</h3>
        <div class="space-y-4">
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.isu_butiran') }}</label>
                <div class="mt-1 bg-gray-50 border border-gray-200 rounded-lg p-3">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $isuRisiko->butiran_isu }}</p>
                </div>
            </div>

            @if($isuRisiko->tindakan_diambil)
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.isu_tindakan') }}</label>
                    <div class="mt-1 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $isuRisiko->tindakan_diambil }}</p>
                    </div>
                </div>
            @endif

            @foreach([
                [__('protege.isu_tarikh_tindakan'), $isuRisiko->tarikh_tindakan ? \Carbon\Carbon::parse($isuRisiko->tarikh_tindakan)->format('d/m/Y') : null],
                [__('protege.isu_tarikh_tutup'), $isuRisiko->tarikh_tutup ? \Carbon\Carbon::parse($isuRisiko->tarikh_tutup)->format('d/m/Y') : null],
            ] as [$label, $value])
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</label>
                    <p class="text-gray-800 mt-1">{{ $value ?? '-' }}</p>
                </div>
            @endforeach

            @if($isuRisiko->catatan)
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.catatan') }}</label>
                    <div class="mt-1 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $isuRisiko->catatan }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection