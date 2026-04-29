@extends('layouts.admin')
@section('title', $syarikatPelaksana->nama_syarikat)
@section('page-title', __('protege.sp_profile'))

@section('content')
<div class="flex items-center justify-between mb-5">
    <a href="{{ route('admin.syarikat-pelaksana.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ __('protege.back_to_list') }}
    </a>
    <a href="{{ route('admin.syarikat-pelaksana.edit', $syarikatPelaksana) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        {{ __('protege.edit') }}
    </a>
</div>

<!-- Company Header -->
<div class="bg-gradient-to-r from-[#1E3A5F] to-[#2d5a8e] rounded-xl p-6 text-white mb-6 shadow-md">
    <div class="flex items-start gap-5">
        <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-2xl font-bold flex-shrink-0">
            {{ substr($syarikatPelaksana->nama_syarikat, 0, 2) }}
        </div>
        <div class="flex-1">
            <h2 class="text-2xl font-bold">{{ $syarikatPelaksana->nama_syarikat }}</h2>
            <div class="text-blue-200 font-mono text-sm">{{ $syarikatPelaksana->id_pelaksana }}</div>
            @if($syarikatPelaksana->projek_kontrak)
                <div class="text-blue-200 text-sm mt-1">{{ $syarikatPelaksana->projek_kontrak }}</div>
            @endif
            <div class="flex flex-wrap gap-2 mt-3">
                @php
                    $danaColor = match($syarikatPelaksana->status_dana) {
                        'Mencukupi' => 'bg-green-500/30 text-green-100',
                        'Perlu Perhatian' => 'bg-yellow-500/30 text-yellow-100',
                        'Kritikal' => 'bg-red-500/30 text-red-100',
                        default => 'bg-white/20 text-white',
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $danaColor }}">{{ __('protege.sp_dana_label') }}: {{ $syarikatPelaksana->status_dana }}</span>
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-white/20">{{ __('protege.sp_pematuhan_label') }}: {{ $syarikatPelaksana->tahap_pematuhan }}</span>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
        <div class="text-2xl font-bold text-[#1E3A5F]">{{ $syarikatPelaksana->kuota_digunakan }}</div>
        <div class="text-xs text-gray-500 mt-1">{{ __('protege.sp_kuota_digunakan') }}</div>
        <div class="text-xs text-gray-400">/ {{ $syarikatPelaksana->kuota_diluluskan }} {{ __('protege.sp_diluluskan') }}</div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
        <div class="text-2xl font-bold text-gray-700">{{ $syarikatPelaksana->graduan_count ?? 0 }}</div>
        <div class="text-xs text-gray-500 mt-1">{{ __('protege.sp_graduan_aktif') }}</div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
        <div class="text-xl font-bold text-green-700">RM {{ number_format($syarikatPelaksana->peruntukan_diluluskan, 0) }}</div>
        <div class="text-xs text-gray-500 mt-1">{{ __('protege.sp_peruntukan_diluluskan') }}</div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
        <div class="text-xl font-bold {{ $syarikatPelaksana->baki_peruntukan < 0 ? 'text-red-600' : 'text-blue-700' }}">
            RM {{ number_format($syarikatPelaksana->baki_peruntukan, 0) }}
        </div>
        <div class="text-xs text-gray-500 mt-1">{{ __('protege.sp_baki_peruntukan') }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Company Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.sp_info') }}</h3>
        <div class="space-y-3">
            <div>
                <label class="text-xs text-gray-400 uppercase font-semibold">{{ __('protege.sp_kuota_obligasi') }}</label>
                <p class="text-sm text-gray-700 mt-0.5">{{ $syarikatPelaksana->jumlah_kuota_obligasi }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-400 uppercase font-semibold">{{ __('protege.sp_peruntukan_diguna') }}</label>
                <p class="text-sm text-gray-700 mt-0.5">RM {{ number_format($syarikatPelaksana->peruntukan_diguna, 2) }}</p>
            </div>

            @if($syarikatPelaksana->peruntukan_diluluskan > 0)
                <div class="border-t border-gray-100 pt-3">
                    @php $usagePct = ($syarikatPelaksana->peruntukan_diguna / $syarikatPelaksana->peruntukan_diluluskan) * 100; @endphp
                    <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                        <span>{{ __('protege.sp_penggunaan_peruntukan') }}</span>
                        <span>{{ round($usagePct) }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $usagePct > 90 ? 'bg-red-500' : ($usagePct > 70 ? 'bg-yellow-500' : 'bg-[#1E3A5F]') }}"
                             style="width: {{ min(100, $usagePct) }}%"></div>
                    </div>
                </div>
            @endif

            <div class="border-t border-gray-100 pt-3 mt-3">
                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-3">{{ __('protege.sp_pic_section') }}</h4>
                <div class="space-y-1.5">
                    <p class="text-sm font-medium text-gray-800">{{ $syarikatPelaksana->pic_syarikat }}</p>
                    <p class="text-sm text-gray-600">{{ $syarikatPelaksana->email_pic }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Surat & Pematuhan -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">{{ __('protege.sp_surat_section') }}</h3>
            <a href="{{ route('admin.status-surat.index', ['id_pelaksana' => $syarikatPelaksana->id_pelaksana]) }}" class="text-xs text-blue-600 hover:underline">{{ __('protege.view') }} &rarr;</a>
        </div>

        <!-- Compliance Badge -->
        <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-100 flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">{{ __('protege.sp_tahap_pematuhan') }}</span>
            @php
                $tpColor = match($syarikatPelaksana->tahap_pematuhan) {
                    'Cemerlang' => 'bg-green-100 text-green-700',
                    'Baik' => 'bg-blue-100 text-blue-700',
                    'Memuaskan', 'Sederhana' => 'bg-yellow-100 text-yellow-700',
                    'Perlu Penambahbaikan' => 'bg-red-100 text-red-700',
                    default => 'bg-gray-100 text-gray-500',
                };
            @endphp
            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium {{ $tpColor }}">{{ $syarikatPelaksana->tahap_pematuhan }}</span>
        </div>

        <!-- Surat Records Table -->
        @if(isset($suratRecords) && $suratRecords->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.nama_graduan') }}</th>
                        <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.ss_jenis_surat') }}</th>
                        <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.status') }}</th>
                        <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.pic') }}</th>
                        <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.tindakan') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($suratRecords->take(10) as $surat)
                        @php
                            $stColor = match($surat->status_surat) {
                                'Selesai' => 'bg-green-100 text-green-700',
                                'Hantar', 'Tandatangan' => 'bg-blue-100 text-blue-700',
                                'Semakan', 'Draft' => 'bg-yellow-100 text-yellow-700',
                                default => 'bg-gray-100 text-gray-500',
                            };
                            $isComplete = $surat->status_surat === 'Selesai';
                            $workflow = \App\Models\StatusSurat::WORKFLOW;
                            $currentIdx = array_search($surat->status_surat, $workflow);
                            $nextStatus = !$isComplete && $currentIdx !== false ? ($workflow[$currentIdx + 1] ?? null) : null;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2.5">
                                <div class="font-medium text-gray-800">{{ $surat->nama_graduan ?? $surat->id_graduan ?? '-' }}</div>
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                <span class="text-xs {{ $surat->jenis_surat === 'Surat Kuning' ? 'text-yellow-700 bg-yellow-50' : 'text-blue-700 bg-blue-50' }} px-2 py-0.5 rounded">{{ $surat->jenis_surat }}</span>
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $stColor }}">{{ $surat->status_surat }}</span>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $currentIdx + 1 }}/{{ count($workflow) }}</div>
                            </td>
                            <td class="px-3 py-2.5 text-center text-xs text-gray-600">{{ $surat->pic_responsible }}</td>
                            <td class="px-3 py-2.5" x-data="{ showFile: false }">
                                @if($nextStatus)
                                    <form method="POST" action="{{ route('admin.status-surat.advance', $surat) }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="flex items-center justify-center gap-1 mb-1">
                                            <button type="submit" class="px-2 py-1 bg-[#1E3A5F] text-white text-xs rounded hover:bg-[#152c47] transition-colors">
                                                &rarr; {{ $nextStatus }}
                                            </button>
                                            <button type="button" @click="showFile = !showFile" class="p-1 text-gray-400 hover:text-blue-600 rounded" title="Attach file">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            </button>
                                            @if($surat->file_attachment)
                                                <a href="{{ asset('storage/' . $surat->file_attachment) }}" target="_blank" class="p-1 text-blue-600 hover:bg-blue-50 rounded" title="{{ $surat->file_name }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.status-surat.show', $surat) }}" class="p-1 text-gray-400 hover:text-blue-600 rounded">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                        </div>
                                        <div x-show="showFile" x-cloak class="mt-1">
                                            <input type="file" name="file_attachment" accept=".pdf,.doc,.docx,.jpg,.png" class="text-[11px] text-gray-500 w-full file:mr-1 file:rounded file:border-0 file:bg-blue-50 file:px-2 file:py-0.5 file:text-[10px] file:text-blue-700">
                                        </div>
                                    </form>
                                @else
                                    <div class="flex items-center justify-center gap-1">
                                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @if($surat->file_attachment)
                                            <a href="{{ asset('storage/' . $surat->file_attachment) }}" target="_blank" class="p-1 text-blue-600 hover:bg-blue-50 rounded" title="{{ $surat->file_name }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.status-surat.show', $surat) }}" class="p-1 text-gray-400 hover:text-blue-600 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-6 text-gray-400 text-sm">{{ __('protege.no_records') }}</div>
        @endif
    </div>
</div>

{{-- Quick Links to Related Modules --}}
<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.tindakan') }}</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        <a href="{{ route('admin.talents.index', ['id_pelaksana' => $syarikatPelaksana->id_pelaksana]) }}"
           class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:bg-blue-50 hover:border-blue-200 transition-colors text-center group">
            <div class="w-10 h-10 bg-blue-50 group-hover:bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-xs font-medium text-gray-700">{{ __('protege.dash_graduan_aktif') }}</span>
        </a>

        <a href="{{ route('admin.kewangan.index', ['id_pelaksana' => $syarikatPelaksana->id_pelaksana]) }}"
           class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:bg-green-50 hover:border-green-200 transition-colors text-center group">
            <div class="w-10 h-10 bg-green-50 group-hover:bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xs font-medium text-gray-700">{{ __('nav.kewangan') }}</span>
        </a>

        <a href="{{ route('admin.status-surat.index', ['id_pelaksana' => $syarikatPelaksana->id_pelaksana]) }}"
           class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:bg-yellow-50 hover:border-yellow-200 transition-colors text-center group">
            <div class="w-10 h-10 bg-yellow-50 group-hover:bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-xs font-medium text-gray-700">{{ __('nav.status_surat') }}</span>
        </a>

        <a href="{{ route('admin.isu-risiko.index', ['id_pelaksana' => $syarikatPelaksana->id_pelaksana]) }}"
           class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:bg-red-50 hover:border-red-200 transition-colors text-center group">
            <div class="w-10 h-10 bg-red-50 group-hover:bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <span class="text-xs font-medium text-gray-700">{{ __('nav.isu_risiko') }}</span>
        </a>

        <a href="{{ route('admin.syarikat-pelaksana.edit', $syarikatPelaksana) }}"
           class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:bg-gray-50 hover:border-gray-200 transition-colors text-center group">
            <div class="w-10 h-10 bg-gray-50 group-hover:bg-gray-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <span class="text-xs font-medium text-gray-700">{{ __('protege.edit') }}</span>
        </a>
    </div>
</div>
@endsection
