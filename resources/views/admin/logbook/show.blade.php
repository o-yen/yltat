@extends('layouts.admin')

@section('title', __('protege.log_title') . ' - ' . $logbook->id_graduan)
@section('page-title', __('protege.log_details'))

@section('content')
<div class="mb-5 flex items-center justify-between">
    <a href="{{ route('admin.logbook.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('protege.back_to_list') }}
    </a>
    <a href="{{ route('admin.logbook.edit', $logbook) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        {{ __('protege.kemaskini') }}
    </a>
</div>

@php
    $logbookStatusLabels = [
        'Dikemukakan' => __('protege.lb_dikemukakan'),
        'Dalam Semakan' => __('protege.lb_dalam_semakan'),
        'Lewat' => __('protege.lb_lewat'),
        'Belum Dikemukakan' => __('protege.lb_belum'),
    ];
    $semakanStatusLabels = [
        'Lulus' => __('protege.semakan_lulus'),
        'Dalam Proses' => __('protege.semakan_dalam_proses'),
        'Perlu Semakan Semula' => __('protege.semakan_perlu_semula'),
        'Belum Disemak' => __('protege.semakan_belum'),
    ];
@endphp

<!-- Header Card -->
<div class="bg-gradient-to-r from-[#1E3A5F] to-[#2d5a8e] rounded-xl p-6 text-white mb-6 shadow-md">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold">{{ $logbook->nama_graduan ?? $logbook->id_graduan }}</h2>
            <div class="text-blue-200 font-mono text-sm mt-1">{{ $logbook->id_graduan }}</div>
            <p class="text-blue-200 mt-1">{{ $logbook->bulan }} {{ $logbook->tahun }}</p>
        </div>
        <div class="flex gap-3">
            @php
                $logbookColors = [
                    'Dikemukakan' => 'bg-green-500/20 text-green-100',
                    'Dalam Semakan' => 'bg-blue-500/20 text-blue-100',
                    'Lewat' => 'bg-yellow-500/20 text-yellow-100',
                    'Belum Dikemukakan' => 'bg-red-500/20 text-red-100',
                ];
                $semakanColors = [
                    'Lulus' => 'bg-green-500/20 text-green-100',
                    'Dalam Proses' => 'bg-blue-500/20 text-blue-100',
                    'Perlu Semakan Semula' => 'bg-yellow-500/20 text-yellow-100',
                    'Belum Disemak' => 'bg-white/10 text-gray-200',
                ];
            @endphp
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $logbookColors[$logbook->status_logbook] ?? 'bg-white/10' }}">
                {{ $logbookStatusLabels[$logbook->status_logbook] ?? $logbook->status_logbook }}
            </span>
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $semakanColors[$logbook->status_semakan] ?? 'bg-white/10' }}">
                {{ $semakanStatusLabels[$logbook->status_semakan] ?? $logbook->status_semakan }}
            </span>
        </div>
    </div>
</div>

<!-- Detail Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.log_graduan_info') }}</h3>
        <div class="space-y-4">
            @foreach([
                [__('protege.id_graduan'), $logbook->id_graduan],
                [__('protege.nama_graduan'), $logbook->nama_graduan],
                [__('protege.penempatan'), $logbook->syarikatPenempatan?->nama_syarikat ?? $logbook->nama_syarikat ?? '-'],
                [__('protege.bulan_tahun'), $logbook->bulan . ' ' . $logbook->tahun],
                [__('protege.log_tarikh_upload'), $logbook->tarikh_upload ? \Carbon\Carbon::parse($logbook->tarikh_upload)->format('d/m/Y') : '-'],
            ] as [$label, $value])
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</label>
                    <p class="text-gray-800 mt-1">{{ $value ?? '-' }}</p>
                </div>
            @endforeach

            @if($logbook->link_file_logbook)
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.log_file_logbook') }}</label>
                    <div class="mt-1">
                        <a href="{{ Str::startsWith($logbook->link_file_logbook, ['http://', 'https://']) ? $logbook->link_file_logbook : Storage::url($logbook->link_file_logbook) }}" target="_blank"
                           class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            {{ __('protege.log_open_file') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.log_semakan_info') }}</h3>
        <div class="space-y-4">
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.log_status_logbook') }}</label>
                <div class="mt-1">
                    @php
                        $badgeLogbook = [
                            'Dikemukakan' => 'bg-green-100 text-green-700',
                            'Dalam Semakan' => 'bg-blue-100 text-blue-700',
                            'Lewat' => 'bg-yellow-100 text-yellow-700',
                            'Belum Dikemukakan' => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $badgeLogbook[$logbook->status_logbook] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $logbookStatusLabels[$logbook->status_logbook] ?? $logbook->status_logbook }}
                    </span>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.log_status_semakan') }}</label>
                <div class="mt-1">
                    @php
                        $badgeSemakan = [
                            'Lulus' => 'bg-green-100 text-green-700',
                            'Dalam Proses' => 'bg-blue-100 text-blue-700',
                            'Perlu Semakan Semula' => 'bg-yellow-100 text-yellow-700',
                            'Belum Disemak' => 'bg-gray-100 text-gray-600',
                        ];
                    @endphp
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $badgeSemakan[$logbook->status_semakan] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $semakanStatusLabels[$logbook->status_semakan] ?? $logbook->status_semakan }}
                    </span>
                </div>
            </div>

            @foreach([
                [__('protege.log_nama_mentor'), $logbook->nama_mentor],
                [__('protege.log_tarikh_semakan'), $logbook->tarikh_semakan ? \Carbon\Carbon::parse($logbook->tarikh_semakan)->format('d/m/Y') : null],
            ] as [$label, $value])
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</label>
                    <p class="text-gray-800 mt-1">{{ $value ?? '-' }}</p>
                </div>
            @endforeach

            @if($logbook->komen_mentor)
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.log_komen_mentor') }}</label>
                    <div class="mt-1 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $logbook->komen_mentor }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
