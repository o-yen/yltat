@extends('layouts.admin')
@section('title', $syarikatPenempatan->nama_syarikat)
@section('page-title', __('protege.spen_profile'))

@section('content')
<div class="flex items-center justify-between mb-5">
    <a href="{{ route('admin.syarikat-penempatan.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ __('protege.back_to_list') }}
    </a>
    <a href="{{ route('admin.syarikat-penempatan.edit', $syarikatPenempatan) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        {{ __('protege.edit') }}
    </a>
</div>

<!-- Header -->
<div class="bg-gradient-to-r from-[#1E3A5F] to-[#2d5a8e] rounded-xl p-6 text-white mb-6 shadow-md">
    <div class="flex items-start gap-5">
        <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-2xl font-bold flex-shrink-0">
            {{ substr($syarikatPenempatan->nama_syarikat, 0, 2) }}
        </div>
        <div class="flex-1">
            <h2 class="text-2xl font-bold">{{ $syarikatPenempatan->nama_syarikat }}</h2>
            <div class="text-blue-200 font-mono text-sm">{{ $syarikatPenempatan->id_syarikat }}</div>
            <div class="flex flex-wrap gap-2 mt-3">
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-white/20">{{ $syarikatPenempatan->sektor_industri }}</span>
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-white/20">{{ $syarikatPenempatan->jenis_syarikat }}</span>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
        <div class="text-2xl font-bold text-[#1E3A5F]">{{ $syarikatPenempatan->jumlah_graduan_ditempatkan }}</div>
        <div class="text-xs text-gray-500 mt-1">{{ __('protege.spen_graduan_count') }}</div>
        <div class="text-xs text-gray-400">/ {{ $syarikatPenempatan->kuota_dipersetujui }} {{ __('protege.spen_kuota_label') }}</div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
        <div class="text-2xl font-bold text-gray-700">{{ $syarikatPenempatan->graduan_count ?? 0 }}</div>
        <div class="text-xs text-gray-500 mt-1">{{ __('protege.spen_graduan_sistem') }}</div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
        <div class="text-2xl font-bold text-green-700">{{ $syarikatPenempatan->training_compliance_pct }}%</div>
        <div class="text-xs text-gray-500 mt-1">{{ __('protege.spen_training_compliance_pct') }}</div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
        @php
            $pmColor = match($syarikatPenempatan->status_pematuhan) {
                'Cemerlang' => 'text-green-700',
                'Baik' => 'text-blue-700',
                'Memuaskan' => 'text-yellow-700',
                default => 'text-red-700',
            };
        @endphp
        <div class="text-lg font-bold {{ $pmColor }}">{{ $syarikatPenempatan->status_pematuhan }}</div>
        <div class="text-xs text-gray-500 mt-1">{{ __('protege.spen_tahap_pematuhan') }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Company Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.spen_maklumat_syarikat') }}</h3>
        <div class="space-y-3">
            <div>
                <label class="text-xs text-gray-400 uppercase font-semibold">{{ __('protege.spen_sektor') }}</label>
                <p class="text-sm text-gray-700 mt-0.5">{{ $syarikatPenempatan->sektor_industri }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-400 uppercase font-semibold">{{ __('protege.spen_laporan_bulanan') }}</label>
                @php
                    $lbColor = match($syarikatPenempatan->laporan_bulanan) {
                        'Lengkap' => 'bg-green-100 text-green-700',
                        'Tertangguh' => 'bg-yellow-100 text-yellow-700',
                        default => 'bg-red-100 text-red-700',
                    };
                @endphp
                <p class="mt-0.5"><span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $lbColor }}">{{ $syarikatPenempatan->laporan_bulanan }}</span></p>
            </div>
            <div class="border-t border-gray-100 pt-3">
                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-3">{{ __('protege.spen_pegawai_perhubungan') }}</h4>
                <div class="space-y-1.5">
                    <p class="text-sm font-medium text-gray-800">{{ $syarikatPenempatan->pic }}</p>
                    <p class="text-sm text-gray-600">{{ $syarikatPenempatan->no_telefon_pic }}</p>
                    <p class="text-sm text-gray-600">{{ $syarikatPenempatan->email_pic }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Training Status -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.spen_training_section') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Session 1 -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">{{ __('protege.spen_sesi1') }}</h4>
                @php
                    $s1Color = match($syarikatPenempatan->soft_skills_sesi1_status) {
                        'Selesai' => 'bg-green-100 text-green-700',
                        'Dalam Perancangan' => 'bg-blue-100 text-blue-700',
                        default => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium {{ $s1Color }}">{{ $syarikatPenempatan->soft_skills_sesi1_status }}</span>
                @if($syarikatPenempatan->soft_skills_sesi1_tarikh)
                    <p class="text-xs text-gray-500 mt-2">{{ __('protege.spen_tarikh_label') }}: {{ $syarikatPenempatan->soft_skills_sesi1_tarikh->format('d/m/Y') }}</p>
                @endif
                <p class="text-xs text-gray-500 mt-1">{{ __('protege.spen_peserta') }}: {{ $syarikatPenempatan->soft_skills_sesi1_peserta }}</p>
            </div>

            <!-- Session 2 -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">{{ __('protege.spen_sesi2') }}</h4>
                @php
                    $s2Color = match($syarikatPenempatan->soft_skills_sesi2_status) {
                        'Selesai' => 'bg-green-100 text-green-700',
                        'Dirancang' => 'bg-blue-100 text-blue-700',
                        default => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium {{ $s2Color }}">{{ $syarikatPenempatan->soft_skills_sesi2_status }}</span>
                @if($syarikatPenempatan->soft_skills_sesi2_tarikh)
                    <p class="text-xs text-gray-500 mt-2">{{ __('protege.spen_tarikh_label') }}: {{ $syarikatPenempatan->soft_skills_sesi2_tarikh->format('d/m/Y') }}</p>
                @endif
                <p class="text-xs text-gray-500 mt-1">{{ __('protege.spen_peserta') }}: {{ $syarikatPenempatan->soft_skills_sesi2_peserta }}</p>
            </div>
        </div>

        <!-- Training Status Summary -->
        <div class="mt-4 bg-gray-50 rounded-lg p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">{{ __('protege.spen_status_training') }}:</span>
                @php
                    $stColor = match($syarikatPenempatan->status_training) {
                        'Cemerlang' => 'bg-green-100 text-green-700',
                        'Baik' => 'bg-blue-100 text-blue-700',
                        'Memuaskan' => 'bg-yellow-100 text-yellow-700',
                        'Dalam Proses' => 'bg-blue-100 text-blue-700',
                        'Perlu Tindakan' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium {{ $stColor }}">{{ $syarikatPenempatan->status_training }}</span>
            </div>
        </div>
    </div>
</div>

@if($syarikatPenempatan->catatan)
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-3">{{ __('protege.catatan') }}</h3>
        <p class="text-gray-600 text-sm leading-relaxed">{{ $syarikatPenempatan->catatan }}</p>
    </div>
@endif
@endsection