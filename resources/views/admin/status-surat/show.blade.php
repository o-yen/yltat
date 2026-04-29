@extends('layouts.admin')

@section('title', __('protege.ss_title') . ' - ' . ($statusSurat->nama_graduan ?? $statusSurat->id_graduan))
@section('page-title', __('protege.ss_show_title'))

@section('content')
<div class="mb-5 flex items-center justify-between">
    <a href="{{ route('admin.status-surat.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('protege.back_to_list') }}
    </a>
    <a href="{{ route('admin.status-surat.edit', $statusSurat) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        {{ __('protege.ss_kemaskini') }}
    </a>
</div>

<!-- Header Card -->
<div class="bg-gradient-to-r from-[#1E3A5F] to-[#2d5a8e] rounded-xl p-6 text-white mb-6 shadow-md">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold">{{ $statusSurat->nama_graduan ?? $statusSurat->id_graduan ?? '-' }}</h2>
            @if($statusSurat->id_graduan)
                <div class="text-blue-200 font-mono text-sm mt-1">{{ $statusSurat->id_graduan }}</div>
            @endif
            <div class="flex gap-3 mt-3">
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $statusSurat->jenis_surat === 'Surat Kuning' ? 'bg-yellow-500/20 text-yellow-100' : 'bg-blue-500/20 text-blue-100' }}">
                    {{ $statusSurat->jenis_surat }}
                </span>
                @php
                    $headerStatusColors = [
                        'Selesai' => 'bg-green-500/20 text-green-100',
                        'Hantar' => 'bg-blue-500/20 text-blue-100',
                        'Tandatangan' => 'bg-blue-500/20 text-blue-100',
                        'Semakan' => 'bg-yellow-500/20 text-yellow-100',
                        'Draft' => 'bg-yellow-500/20 text-yellow-100',
                        'Belum Mula' => 'bg-white/10 text-gray-200',
                    ];
                @endphp
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $headerStatusColors[$statusSurat->status_surat] ?? 'bg-white/10' }}">
                    {{ $statusSurat->status_surat }}
                </span>
            </div>
        </div>
        <div class="text-right">
            <p class="text-sm text-blue-200">{{ __('protege.ss_pic') }}</p>
            <p class="font-semibold">{{ $statusSurat->pic_responsible }}</p>
            @if($statusSurat->syarikatPelaksana)
                <p class="text-sm text-blue-200 mt-2">{{ $statusSurat->syarikatPelaksana->nama_syarikat }}</p>
            @endif
        </div>
    </div>
</div>

<!-- Workflow Stepper -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <h3 class="font-semibold text-gray-800 mb-5">{{ __('protege.ss_aliran_kerja') }}</h3>
    @php
        $steps = ['Belum Mula', 'Draft', 'Semakan', 'Tandatangan', 'Hantar', 'Selesai'];
        $stepLabels = [
            'Belum Mula' => __('protege.wf_belum_mula'),
            'Draft' => __('protege.wf_draft'),
            'Semakan' => __('protege.wf_semakan'),
            'Tandatangan' => __('protege.wf_tandatangan'),
            'Hantar' => __('protege.wf_hantar'),
            'Selesai' => __('protege.wf_selesai'),
        ];
        $currentIndex = array_search($statusSurat->status_surat, $steps);
        if ($currentIndex === false) $currentIndex = 0;
        $dateFields = [
            'tarikh_mula_proses',
            'tarikh_draft',
            'tarikh_semakan',
            'tarikh_tandatangan',
            'tarikh_hantar',
            'tarikh_siap',
        ];
    @endphp
    <div class="flex items-center justify-between relative">
        {{-- Connecting line --}}
        <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200 z-0"></div>
        <div class="absolute top-5 left-0 h-0.5 bg-[#1E3A5F] z-0" style="width: {{ $currentIndex > 0 ? ($currentIndex / (count($steps) - 1)) * 100 : 0 }}%"></div>

        @foreach($steps as $i => $step)
            <div class="relative z-10 flex flex-col items-center" style="width: {{ 100 / count($steps) }}%">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold
                    {{ $i < $currentIndex ? 'bg-green-500 text-white' : ($i === $currentIndex ? 'bg-[#1E3A5F] text-white ring-4 ring-[#1E3A5F]/20' : 'bg-gray-200 text-gray-500') }}">
                    @if($i < $currentIndex)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        {{ $i + 1 }}
                    @endif
                </div>
                <p class="text-xs font-medium mt-2 text-center {{ $i === $currentIndex ? 'text-[#1E3A5F] font-bold' : ($i < $currentIndex ? 'text-green-600' : 'text-gray-400') }}">
                    {{ $stepLabels[$step] ?? $step }}
                </p>
                @if(isset($dateFields[$i]) && $statusSurat->{$dateFields[$i]})
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($statusSurat->{$dateFields[$i]})->format('d/m/Y') }}</p>
                @endif
            </div>
        @endforeach
    </div>
</div>

<!-- Detail Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.ss_maklumat_surat') }}</h3>
        <div class="space-y-4">
            @foreach([
                [__('protege.ss_jenis_surat'), $statusSurat->jenis_surat],
                [__('protege.id_graduan'), $statusSurat->id_graduan],
                [__('protege.nama_graduan'), $statusSurat->nama_graduan],
                [__('protege.ss_status_surat'), $statusSurat->status_surat],
                [__('protege.ss_pic'), $statusSurat->pic_responsible],
                [__('protege.pelaksana'), $statusSurat->syarikatPelaksana->nama_syarikat ?? '-'],
            ] as [$label, $value])
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</label>
                    <p class="text-gray-800 mt-1">{{ $value ?? '-' }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.ss_tarikh_proses') }}</h3>
        <div class="space-y-4">
            @foreach([
                [__('protege.ss_tarikh_mula'), $statusSurat->tarikh_mula_proses],
                [__('protege.ss_tarikh_draft'), $statusSurat->tarikh_draft],
                [__('protege.ss_tarikh_semakan'), $statusSurat->tarikh_semakan],
                [__('protege.ss_tarikh_tandatangan'), $statusSurat->tarikh_tandatangan],
                [__('protege.ss_tarikh_hantar'), $statusSurat->tarikh_hantar],
                [__('protege.ss_tarikh_siap'), $statusSurat->tarikh_siap],
            ] as [$label, $value])
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</label>
                    <p class="text-gray-800 mt-1">{{ $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '-' }}</p>
                </div>
            @endforeach

            @if($statusSurat->tarikh_mula_proses && $statusSurat->tarikh_siap)
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.ss_tempoh_sla') }}</label>
                    @php
                        $days = \Carbon\Carbon::parse($statusSurat->tarikh_mula_proses)->diffInDays(\Carbon\Carbon::parse($statusSurat->tarikh_siap));
                    @endphp
                    <p class="text-gray-800 mt-1 font-semibold {{ $days > 14 ? 'text-red-600' : ($days > 7 ? 'text-yellow-600' : 'text-green-600') }}">
                        {{ $days }} {{ __('protege.ss_hari') }}
                    </p>
                </div>
            @endif
        </div>

        @if($statusSurat->isu_halangan)
            <div class="mt-4">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.ss_isu_halangan') }}</label>
                <div class="mt-1 bg-red-50 border border-red-200 rounded-lg p-3">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $statusSurat->isu_halangan }}</p>
                </div>
            </div>
        @endif

        @if($statusSurat->catatan)
            <div class="mt-4">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.catatan') }}</label>
                <div class="mt-1 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $statusSurat->catatan }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
