@extends('layouts.admin')

@section('title', __('protege.dash_rakan_title'))
@section('page-title', __('protege.dash_rakan_title'))

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
        <h1 class="text-2xl font-bold text-white">{{ $syarikat->nama_syarikat ?? $syarikat->company_name ?? '-' }}</h1>
        <p class="text-blue-200 text-sm mt-1">{{ __('protege.dash_rakan_subtitle') }}</p>
    </div>
</div>

{{-- KPI Cards --}}
@php
    $trainingTotal = count($trainingRecords ?? []);
    $trainingCompleted = collect($trainingRecords ?? [])->where('status', 'selesai')->count();
    $trainingCompliance = $trainingTotal > 0 ? round(($trainingCompleted / $trainingTotal) * 100, 1) : 0;
@endphp
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
    {{-- Graduan --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-[#1E3A5F]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('protege.dash_jumlah_graduan') }}</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($graduanCount) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Purata Kehadiran --}}
    @php
        $kehadiranColor = ($avgKehadiran ?? 0) >= 80 ? 'green' : (($avgKehadiran ?? 0) >= 60 ? 'yellow' : 'red');
    @endphp
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-{{ $kehadiranColor }}-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('protege.dash_purata_kehadiran') }}</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($avgKehadiran ?? 0, 1) }}<span class="text-base font-normal text-gray-400">%</span></p>
            </div>
            <div class="w-12 h-12 bg-{{ $kehadiranColor }}-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-{{ $kehadiranColor }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Purata Prestasi --}}
    @php
        $prestasiColor = ($avgPrestasi ?? 0) >= 80 ? 'green' : (($avgPrestasi ?? 0) >= 60 ? 'yellow' : 'red');
    @endphp
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-{{ $prestasiColor }}-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('protege.dash_purata_prestasi') }}</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($avgPrestasi ?? 0, 1) }}<span class="text-base font-normal text-gray-400">%</span></p>
            </div>
            <div class="w-12 h-12 bg-{{ $prestasiColor }}-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-{{ $prestasiColor }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Training Compliance --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-indigo-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('protege.dash_pematuhan_latihan') }}</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $trainingCompliance }}<span class="text-base font-normal text-gray-400">%</span></p>
            </div>
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ min($trainingCompliance, 100) }}%"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1">{{ $trainingCompleted }}/{{ $trainingTotal }} {{ __('protege.dash_sesi_selesai') }}</p>
        </div>
    </div>
</div>

{{-- Tables Row: Attendance + Logbook --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Recent Attendance --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.dash_kehadiran_terkini') }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">{{ __('protege.dash_graduan') }}</th>
                        <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">{{ __('protege.tarikh') }}</th>
                        <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">{{ __('protege.status') }}</th>
                        <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">{{ __('protege.dash_masa_masuk') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($kehadiranRecords as $rec)
                        @php
                            $hadirColor = match($rec->status ?? 'hadir') {
                                'hadir'       => 'green',
                                'lewat'       => 'yellow',
                                'tidak_hadir' => 'red',
                                'mc', 'cuti'  => 'blue',
                                default       => 'gray',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-2.5 text-gray-700">{{ $rec->graduan?->full_name ?? $rec->nama_graduan ?? '-' }}</td>
                            <td class="py-2.5 text-center text-gray-600 text-xs">{{ $rec->tarikh?->format('d/m/Y') ?? $rec->created_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="py-2.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $hadirColor }}-100 text-{{ $hadirColor }}-700">
                                    {{ ucfirst(str_replace('_', ' ', $rec->status ?? 'hadir')) }}
                                </span>
                            </td>
                            <td class="py-2.5 text-center text-gray-600 text-xs">{{ $rec->masa_masuk ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-400 text-sm">{{ __('protege.dash_tiada_kehadiran') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Logbook Status --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.dash_logbook_status') }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">{{ __('protege.dash_graduan') }}</th>
                        <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">{{ __('protege.bulan') }}</th>
                        <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">{{ __('protege.status') }}</th>
                        <th class="text-left pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">{{ __('protege.ss_tarikh_hantar') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logbookRecords as $log)
                        @php
                            $logColor = match($log->status ?? 'pending') {
                                'disahkan', 'approved'  => 'green',
                                'pending', 'draft'      => 'yellow',
                                'ditolak', 'rejected'   => 'red',
                                default                 => 'gray',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-2.5 text-gray-700">{{ $log->graduan?->full_name ?? $log->nama_graduan ?? '-' }}</td>
                            <td class="py-2.5 text-center text-gray-600 text-xs">{{ $log->minggu ?? $log->week_number ?? '-' }}</td>
                            <td class="py-2.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $logColor }}-100 text-{{ $logColor }}-700">
                                    {{ ucfirst(str_replace('_', ' ', $log->status ?? 'pending')) }}
                                </span>
                            </td>
                            <td class="py-2.5 text-gray-500 text-xs">{{ $log->tarikh_hantar?->format('d/m/Y') ?? $log->created_at?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-400 text-sm">{{ __('protege.dash_tiada_logbook') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Training Sessions --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.dash_sesi_latihan') }}</h3>
    @if(count($trainingRecords ?? []) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($trainingRecords as $training)
                @php
                    $tColor = match($training->status ?? 'pending') {
                        'selesai', 'completed' => 'green',
                        'sedang_berjalan'      => 'blue',
                        'pending', 'scheduled' => 'yellow',
                        'dibatal', 'cancelled' => 'red',
                        default                => 'gray',
                    };
                @endphp
                <div class="border border-gray-100 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-800">{{ $training->nama_latihan ?? $training->title ?? '-' }}</h4>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $tColor }}-100 text-{{ $tColor }}-700 flex-shrink-0 ml-2">
                            {{ ucfirst(str_replace('_', ' ', $training->status ?? 'pending')) }}
                        </span>
                    </div>
                    <div class="space-y-1 text-xs text-gray-500">
                        @if($training->tarikh_mula ?? $training->start_date ?? null)
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ ($training->tarikh_mula ?? $training->start_date)?->format('d/m/Y') ?? '-' }}
                            </p>
                        @endif
                        @if($training->lokasi ?? $training->location ?? null)
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $training->lokasi ?? $training->location ?? '-' }}
                            </p>
                        @endif
                        @if($training->pengajar ?? $training->trainer ?? null)
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $training->pengajar ?? $training->trainer ?? '-' }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="text-sm">{{ __('protege.dash_tiada_latihan') }}</p>
        </div>
    @endif
</div>
@endsection
