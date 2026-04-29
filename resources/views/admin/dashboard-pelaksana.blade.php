@extends('layouts.admin')

@section('title', __('protege.dash_pelaksana_title'))
@section('page-title', __('protege.dash_pelaksana_title'))

@section('content')
{{-- Company Header --}}
<div class="relative rounded-2xl overflow-hidden mb-6 px-8 py-6"
     style="background: linear-gradient(135deg, #1E3A5F 0%, #2d5a8e 50%, #1E3A5F 100%);">
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 400 120" preserveAspectRatio="none">
            <path d="M0,60 Q100,20 200,60 T400,60 V120 H0 Z" fill="white"/>
        </svg>
    </div>
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">{{ $pelaksana->nama_syarikat ?? '-' }}</h1>
        <p class="text-blue-200 text-sm mt-1">
            {{ __('protege.kod') }}: <span class="font-semibold text-white">{{ $pelaksana->id_pelaksana ?? '-' }}</span>
            <span class="mx-2">&middot;</span>
            {{ __('protege.dash_pelaksana_subtitle') }}
        </p>
    </div>
</div>

{{-- KPI Cards --}}
@php
    $peruntukan = $pelaksana->peruntukan_diluluskan ?? 0;
    $baki = $pelaksana->baki_peruntukan ?? 0;
    $danaStatus = $pelaksana->status_dana ?? 'Kritikal';
    $danaColor = match($danaStatus) {
        'Mencukupi' => 'green',
        'Perlu Perhatian' => 'yellow',
        'Kritikal' => 'red',
        default => 'gray',
    };
@endphp
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
    {{-- Graduan Count --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-[#1E3A5F]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('protege.dash_jumlah_graduan') }}</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($graduanCount) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- Budget --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('protege.dash_peruntukan_baki') }}</p>
                <p class="text-xl font-bold text-gray-800 mt-2">RM {{ number_format($peruntukan) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('protege.dash_baki') }}: <span class="font-semibold {{ $baki >= 0 ? 'text-green-600' : 'text-red-600' }}">RM {{ number_format($baki) }}</span></p>
            </div>
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
            </div>
        </div>
    </div>

    {{-- Bayaran Selesai / Lewat --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('protege.dash_bayaran_selesai_lewat') }}</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ number_format($bayaranSelesai) }}
                    <span class="text-base font-normal text-gray-400">/ </span>
                    <span class="text-lg text-red-500 font-semibold">{{ number_format($bayaranLewat) }}</span>
                </p>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">{{ __('protege.dash_selesai') }} / {{ __('protege.dash_lewat') }}</p>
    </div>

    {{-- Dana Status --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-{{ $danaColor }}-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('protege.dash_status_dana') }}</p>
                <p class="text-xl font-bold text-{{ $danaColor }}-600 mt-2">{{ $danaStatus }}</p>
            </div>
            <div class="w-12 h-12 bg-{{ $danaColor }}-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-{{ $danaColor }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Tables Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Recent Payment Records --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.dash_rekod_bayaran') }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left pb-3 text-gray-500 font-medium text-xs uppercase">{{ __('protege.id_graduan') }}</th>
                        <th class="text-left pb-3 text-gray-500 font-medium text-xs uppercase">{{ __('protege.bulan') }}</th>
                        <th class="text-right pb-3 text-gray-500 font-medium text-xs uppercase">{{ __('protege.kw_elaun_prorate') }}</th>
                        <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase">{{ __('protege.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($kewanganRecords as $rec)
                        @php
                            $stColor = match($rec->status_bayaran) {
                                'Selesai' => 'green',
                                'Lewat' => 'red',
                                'Dalam Proses' => 'yellow',
                                default => 'gray',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="py-2.5 text-gray-700 font-medium text-xs">{{ $rec->id_graduan }}</td>
                            <td class="py-2.5 text-gray-600 text-xs">{{ $rec->bulan }}</td>
                            <td class="py-2.5 text-right text-gray-800 font-medium">RM {{ number_format($rec->elaun_prorate, 2) }}</td>
                            <td class="py-2.5 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $stColor }}-100 text-{{ $stColor }}-700">{{ $rec->status_bayaran }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-400 text-sm">{{ __('protege.dash_tiada_bayaran') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Letter Status --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.dash_rekod_surat') }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left pb-3 text-gray-500 font-medium text-xs uppercase">{{ __('protege.dash_graduan') }}</th>
                        <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase">{{ __('protege.ss_jenis_surat') }}</th>
                        <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase">{{ __('protege.status') }}</th>
                        <th class="text-left pb-3 text-gray-500 font-medium text-xs uppercase">{{ __('protege.tarikh') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($suratRecords as $surat)
                        @php
                            $ssColor = match($surat->status_surat) {
                                'Selesai' => 'green',
                                'Hantar', 'Tandatangan' => 'blue',
                                'Semakan', 'Draft' => 'yellow',
                                default => 'gray',
                            };
                            $jnColor = $surat->jenis_surat === 'Surat Kuning' ? 'yellow' : 'blue';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="py-2.5 text-gray-700 text-xs">{{ $surat->nama_graduan ?? $surat->id_graduan ?? '-' }}</td>
                            <td class="py-2.5 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $jnColor }}-100 text-{{ $jnColor }}-700">{{ $surat->jenis_surat }}</span>
                            </td>
                            <td class="py-2.5 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $ssColor }}-100 text-{{ $ssColor }}-700">{{ $surat->status_surat }}</span>
                            </td>
                            <td class="py-2.5 text-gray-500 text-xs">{{ $surat->tarikh_mula_proses?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-400 text-sm">{{ __('protege.dash_tiada_surat') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
