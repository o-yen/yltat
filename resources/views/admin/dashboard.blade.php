@extends('layouts.admin')

@section('title', __('protege.dash_title_eksekutif'))
@section('page-title', __('nav.dashboard'))

@section('content')
{{-- Dark Gradient Header --}}
<div class="relative rounded-2xl overflow-hidden mb-6 px-8 py-6"
     style="background: linear-gradient(135deg, #1E3A5F 0%, #2d5a8e 50%, #1E3A5F 100%);">
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 400 120" preserveAspectRatio="none">
            <path d="M0,60 Q100,20 200,60 T400,60 V120 H0 Z" fill="white"/>
        </svg>
    </div>
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">{{ __('protege.dash_executive') }}</h1>
        <p class="text-blue-200 text-sm mt-1">{{ __('protege.dash_summary') }} {{ now()->translatedFormat('d M Y, H:i') }}</p>
    </div>
</div>

{{-- Row 1: 4 Gradient KPI Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
    {{-- Card 1: Total Graduan Aktif — Blue gradient --}}
    <a href="{{ route('admin.talents.index') }}" class="relative overflow-hidden rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 block"
       style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 rounded-full opacity-20" style="background: rgba(255,255,255,0.2);"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-white/80 text-xs font-medium uppercase tracking-wider">{{ __('protege.dash_graduan_aktif') }}</p>
            <p class="text-white text-3xl font-bold mt-1">{{ number_format($totalGraduanAktif) }}</p>
            <p class="text-white/60 text-xs mt-2">{{ $totalTalents ?? 0 }} {{ __('common.total') }}</p>
        </div>
    </a>

    {{-- Card 2: Surat Kuning — Amber/Yellow gradient --}}
    <a href="{{ route('admin.status-surat.index', ['jenis_surat' => 'Surat Kuning']) }}" class="relative overflow-hidden rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 block"
       style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);">
        <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 rounded-full opacity-20" style="background: rgba(255,255,255,0.2);"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-white/80 text-xs font-medium uppercase tracking-wider">{{ __('protege.dash_surat_kuning_siap') }}</p>
            <p class="text-white text-3xl font-bold mt-1">{{ number_format($suratKuningSelesai) }} <span class="text-lg font-normal text-white/60">/ {{ $totalSuratKuning }}</span></p>
            @if($totalSuratKuning > 0)
                <div class="mt-3">
                    <div class="w-full bg-white/20 rounded-full h-1.5">
                        <div class="bg-white h-1.5 rounded-full" style="width: {{ round(($suratKuningSelesai / $totalSuratKuning) * 100) }}%"></div>
                    </div>
                    <p class="text-white/60 text-xs mt-1">{{ round(($suratKuningSelesai / $totalSuratKuning) * 100) }}% {{ __('protege.dash_selesai') }}</p>
                </div>
            @endif
        </div>
    </a>

    {{-- Card 3: Surat Biru — Teal/Cyan gradient --}}
    <a href="{{ route('admin.status-surat.index', ['jenis_surat' => 'Surat Biru']) }}" class="relative overflow-hidden rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 block"
       style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
        <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 rounded-full opacity-20" style="background: rgba(255,255,255,0.2);"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-white/80 text-xs font-medium uppercase tracking-wider">{{ __('protege.dash_surat_biru_siap') }}</p>
            <p class="text-white text-3xl font-bold mt-1">{{ number_format($suratBiruSelesai) }} <span class="text-lg font-normal text-white/60">/ {{ $totalSuratBiru }}</span></p>
            @if($totalSuratBiru > 0)
                <div class="mt-3">
                    <div class="w-full bg-white/20 rounded-full h-1.5">
                        <div class="bg-white h-1.5 rounded-full" style="width: {{ round(($suratBiruSelesai / $totalSuratBiru) * 100) }}%"></div>
                    </div>
                    <p class="text-white/60 text-xs mt-1">{{ round(($suratBiruSelesai / $totalSuratBiru) * 100) }}% {{ __('protege.dash_selesai') }}</p>
                </div>
            @endif
        </div>
    </a>

    {{-- Card 4: KPI Index — Green/Emerald gradient --}}
    @php $kpiScore = $latestKpi->avg_prestasi_score ?? 0; @endphp
    <a href="{{ route('admin.kpi-dashboard.index') }}" class="relative overflow-hidden rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 block"
       style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
        <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 rounded-full opacity-20" style="background: rgba(255,255,255,0.2);"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <p class="text-white/80 text-xs font-medium uppercase tracking-wider">{{ __('protege.dash_kpi_index') }}</p>
            <p class="text-white text-3xl font-bold mt-1">{{ number_format($kpiScore, 1) }} <span class="text-lg font-normal text-white/60">/10</span></p>
            <p class="text-white/60 text-xs mt-2">{{ $latestKpi ? $latestKpi->bulan : '-' }}</p>
        </div>
    </a>
</div>

{{-- Row 2: Charts Side by Side --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Left: Graduates by Placement Company — Budget Comparison --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.dash_graduan_per_syarikat') }} &mdash; Perbandingan Bajet</h3>
        <div id="chart-budget-comparison"></div>
    </div>

    {{-- Right: Sector & Industries Donut --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Sektor &amp; Industri</h3>
        <div id="chart-sektor-donut"></div>
    </div>
</div>

{{-- Row 3: Monthly Trend with 3 Summary Cards --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">Trend Bulanan &mdash; Penempatan &amp; Kehadiran</h3>

    {{-- 3 Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
        {{-- Card 1: Kehadiran --}}
        <div class="flex items-center gap-4 bg-gray-50 rounded-xl p-4 border-l-4 border-green-500">
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ round(($avgKehadiranAll ?? 0) * 100, 1) }}%</p>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Kehadiran</p>
            </div>
        </div>
        {{-- Card 2: Prestasi --}}
        <div class="flex items-center gap-4 bg-gray-50 rounded-xl p-4 border-l-4 border-blue-500">
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ round($avgPrestasiAll ?? 0, 1) }}</p>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Prestasi</p>
            </div>
        </div>
        {{-- Card 3: Compliance --}}
        <div class="flex items-center gap-4 bg-gray-50 rounded-xl p-4 border-l-4 border-purple-500">
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ round($avgComplianceAll ?? 0, 1) }}%</p>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Compliance</p>
            </div>
        </div>
    </div>

    {{-- Line Chart --}}
    <div id="chart-monthly-trend"></div>
</div>

{{-- Row 4: Graduate Tracker + Issues --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Left: Graduate Tracker --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Penjejakan Graduan</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Nama</th>
                        <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Status</th>
                        <th class="text-left pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Syarikat</th>
                        <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Kehadiran</th>
                        <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Skor</th>
                        <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Logbook</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse(($graduateTracker ?? collect())->take(10) as $grad)
                        @php
                            $att = ($grad->latest_kehadiran ?? 0) * 100;
                            $attColor = $att >= 85 ? 'green' : ($att >= 75 ? 'yellow' : 'red');
                            $score = $grad->latest_skor ?? 0;
                            $scoreColor = $score >= 8 ? 'green' : ($score >= 6 ? 'blue' : 'red');
                            $logbookStatus = $grad->latest_logbook ?? null;
                            $logbookLabel = match($logbookStatus) {
                                'Dikemukakan' => 'OK',
                                'Dalam Semakan' => 'OK',
                                'Lewat' => 'LEWAT',
                                default => 'TIADA',
                            };
                            $logbookColor = match($logbookStatus) {
                                'Dikemukakan', 'Dalam Semakan' => 'green',
                                'Lewat' => 'yellow',
                                default => 'red',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-2.5">
                                @if($grad->id)
                                <a href="{{ route('admin.talents.show', $grad->id) }}" class="text-blue-600 hover:underline font-medium text-sm">
                                    {{ $grad->full_name }}
                                </a>
                                @else
                                <span class="font-medium text-sm text-gray-700">{{ $grad->full_name }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    {{ ($grad->status_aktif ?? '') === 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $grad->status_aktif ?? '-' }}
                                </span>
                            </td>
                            <td class="py-2.5 text-gray-600 text-xs">{{ Str::limit($grad->company_name ?? '-', 20) }}</td>
                            <td class="py-2.5 text-center">
                                <span class="text-xs font-semibold text-{{ $attColor }}-600">{{ round($att, 0) }}%</span>
                            </td>
                            <td class="py-2.5 text-center">
                                <span class="text-xs font-semibold text-{{ $scoreColor }}-600">{{ number_format($score, 1) }}</span>
                            </td>
                            <td class="py-2.5 text-center">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-{{ $logbookColor }}-100 text-{{ $logbookColor }}-700">
                                    {{ $logbookLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-400 text-sm">{{ __('protege.no_records') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Right: Isu & Notifikasi --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 bg-red-500 rounded-full inline-block"></span>
            Amaran &amp; Notifikasi
            @if(($kritikalCount ?? 0) > 0)
                <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $kritikalCount }} {{ __('protege.dash_kritikal') }}</span>
            @endif
            <a href="{{ route('admin.isu-risiko.index') }}" class="ml-auto text-xs text-blue-600 hover:underline">{{ __('protege.view') }} &rarr;</a>
        </h3>
        <div class="space-y-2 max-h-[380px] overflow-y-auto pr-1">
            @forelse(($activeIssues ?? collect())->take(8) as $issue)
                @php
                    $levelColor = match(strtolower($issue->tahap_risiko ?? 'rendah')) {
                        'kritikal' => 'red',
                        'tinggi'   => 'orange',
                        'sederhana'=> 'yellow',
                        'rendah'   => 'green',
                        default    => 'gray',
                    };
                @endphp
                <a href="{{ route('admin.isu-risiko.show', $issue) }}" class="flex items-start gap-3 p-3 bg-{{ $levelColor }}-50 rounded-lg hover:bg-{{ $levelColor }}-100 transition-colors">
                    <div class="w-8 h-8 bg-{{ $levelColor }}-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-{{ $levelColor }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ Str::limit($issue->butiran_isu, 60) }}</p>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-{{ $levelColor }}-100 text-{{ $levelColor }}-700 flex-shrink-0">
                                {{ $issue->tahap_risiko }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $issue->id_isu }} &middot; {{ $issue->kategori_isu }} &middot; {{ $issue->tarikh_isu?->diffForHumans() }}</p>
                    </div>
                </a>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm">{{ __('protege.no_records') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Row 5: Syarikat Pelaksana Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.dash_syarikat_pelaksana') }}</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">{{ __('protege.sp_nama_syarikat') }}</th>
                    <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">{{ __('protege.dash_kuota') }}</th>
                    <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">{{ __('protege.dash_status_dana') }}</th>
                    <th class="text-center pb-3 text-gray-500 font-medium text-xs uppercase tracking-wider">{{ __('protege.ss_title') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pelaksanaSummary as $sp)
                    @php
                        $danaColor = match($sp->status_dana) {
                            'Mencukupi' => 'green',
                            'Perlu Perhatian' => 'yellow',
                            'Kritikal' => 'red',
                            default => 'gray',
                        };
                        $skColor = match($sp->status_surat_kuning) {
                            'Siap' => 'text-green-600', 'Dalam Proses' => 'text-blue-600', default => 'text-gray-400',
                        };
                        $sbColor = match($sp->status_surat_biru) {
                            'Siap' => 'text-green-600', 'Dalam Proses' => 'text-blue-600', default => 'text-gray-400',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('admin.syarikat-pelaksana.show', $sp) }}'">
                        <td class="py-2.5 text-gray-700 font-medium">{{ $sp->nama_syarikat }}</td>
                        <td class="py-2.5 text-center text-gray-600">{{ $sp->kuota_digunakan }}/{{ $sp->kuota_diluluskan }}</td>
                        <td class="py-2.5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $danaColor }}-100 text-{{ $danaColor }}-700">
                                {{ $sp->status_dana }}
                            </span>
                        </td>
                        <td class="py-2.5 text-center">
                            <span class="text-xs {{ $skColor }}">K: {{ $sp->status_surat_kuning }}</span>
                            <span class="text-xs {{ $sbColor }} ml-1">B: {{ $sp->status_surat_biru }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-400 text-sm">{{ __('protege.no_records') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Row 6: Aktiviti Terkini --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.dash_aktiviti_terkini') }}</h3>
    <div class="space-y-3 max-h-[380px] overflow-y-auto pr-1">
        @forelse(collect($recentActivity)->take(10) as $log)
            <div class="flex items-start gap-3 group">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
                    {{ $log->action_type === 'create' ? 'bg-green-100' : '' }}
                    {{ $log->action_type === 'update' ? 'bg-blue-100' : '' }}
                    {{ $log->action_type === 'delete' ? 'bg-red-100' : '' }}
                    {{ !in_array($log->action_type ?? '', ['create','update','delete']) ? 'bg-gray-100' : '' }}
                ">
                    @if($log->action_type === 'create')
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    @elseif($log->action_type === 'update')
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    @elseif($log->action_type === 'delete')
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    @else
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-700">
                        <span class="font-medium">{{ $log->user?->full_name ?? 'Sistem' }}</span>
                        <span class="text-gray-400 mx-1">&middot;</span>
                        <span>{{ ucfirst($log->action_type ?? '') }} {{ ucfirst($log->module_name ?? '') }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $log->created_at?->diffForHumans() }}</p>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-400 text-sm">{{ __('protege.no_records') }}</div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
const primaryColor = '#1E3A5F';
const chartFont = 'Inter';

// ===== Chart 1: Budget Comparison — Grouped Horizontal Bar =====
@php
    $budgetLabels     = ($budgetComparison ?? collect())->pluck('nama_syarikat')->toArray();
    $budgetAllocated  = ($budgetComparison ?? collect())->pluck('peruntukan_diluluskan')->toArray();
    $budgetUsed       = ($budgetComparison ?? collect())->pluck('peruntukan_diguna')->toArray();
@endphp
@if(count($budgetLabels) > 0)
new ApexCharts(document.querySelector('#chart-budget-comparison'), {
    series: [
        { name: 'Allocated (RM)', data: @json($budgetAllocated) },
        { name: 'Used (RM)', data: @json($budgetUsed) }
    ],
    chart: { type: 'bar', height: 360, toolbar: { show: false }, fontFamily: chartFont },
    colors: ['#1E3A5F', '#f59e0b'],
    plotOptions: { bar: { borderRadius: 4, horizontal: true, barHeight: '55%', dataLabels: { position: 'top' } } },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 4, xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
    xaxis: {
        categories: @json($budgetLabels),
        labels: { style: { colors: '#94a3b8', fontSize: '11px' }, formatter: (v) => 'RM ' + Number(v).toLocaleString() }
    },
    yaxis: { labels: { style: { colors: '#334155', fontSize: '11px' }, maxWidth: 180 } },
    tooltip: { y: { formatter: (v) => 'RM ' + Number(v).toLocaleString('en-MY', { minimumFractionDigits: 2 }) } },
    legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' }
}).render();
@else
document.querySelector('#chart-budget-comparison').innerHTML = '<p class="text-center text-gray-400 text-sm py-12">{{ __("protege.no_records") }}</p>';
@endif

// ===== Chart 2: Sektor & Industri — Donut =====
@php
    $sektorLabels = ($sektorData ?? collect())->pluck('sektor_industri')->toArray();
    $sektorValues = ($sektorData ?? collect())->pluck('total_graduan')->toArray();
@endphp
@if(count($sektorLabels) > 0)
new ApexCharts(document.querySelector('#chart-sektor-donut'), {
    series: @json($sektorValues),
    chart: { type: 'donut', height: 360, fontFamily: chartFont },
    labels: @json($sektorLabels),
    colors: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#43e97b', '#f6d365', '#fda085', '#a78bfa', '#34d399'],
    legend: { position: 'right', fontSize: '12px', offsetY: 0 },
    dataLabels: { enabled: true, formatter: (v) => Math.round(v) + '%' },
    plotOptions: { pie: { donut: { size: '60%', labels: { show: true, total: { show: true, label: 'Jumlah Graduan', fontSize: '14px', fontWeight: 600 } } } } },
    responsive: [{ breakpoint: 640, options: { legend: { position: 'bottom' } } }]
}).render();
@else
document.querySelector('#chart-sektor-donut').innerHTML = '<p class="text-center text-gray-400 text-sm py-12">{{ __("protege.no_records") }}</p>';
@endif

// ===== Chart 3: Monthly Trend — Multi-Line =====
@php
    $trendLabels       = ($kpiHistory ?? collect())->pluck('bulan')->toArray();
    $trendKehadiran    = ($kpiHistory ?? collect())->pluck('avg_kehadiran_pct')->map(fn($v) => round(($v ?? 0) * 100, 1))->toArray();
    $trendPrestasi     = ($kpiHistory ?? collect())->pluck('avg_prestasi_score')->map(fn($v) => round(($v ?? 0) * 10, 1))->toArray();
    $trendLogbook      = ($kpiHistory ?? collect())->pluck('logbook_submitted_pct')->map(fn($v) => round($v ?? 0, 1))->toArray();
@endphp
@if(count($trendLabels) > 0)
new ApexCharts(document.querySelector('#chart-monthly-trend'), {
    series: [
        { name: 'Kehadiran (%)', data: @json($trendKehadiran) },
        { name: 'Prestasi (scaled %)', data: @json($trendPrestasi) },
        { name: 'Logbook (%)', data: @json($trendLogbook) }
    ],
    chart: { type: 'line', height: 340, toolbar: { show: false }, fontFamily: chartFont },
    colors: ['#10b981', '#3b82f6', '#8b5cf6'],
    stroke: { curve: 'smooth', width: 3 },
    markers: { size: 5, strokeColors: '#fff', strokeWidth: 2 },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
    xaxis: { categories: @json($trendLabels), labels: { style: { colors: '#94a3b8', fontSize: '11px' } } },
    yaxis: { min: 0, max: 100, labels: { formatter: (v) => v + '%', style: { colors: '#94a3b8', fontSize: '11px' } } },
    tooltip: { y: { formatter: (v) => v + '%' } },
    legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' }
}).render();
@else
document.querySelector('#chart-monthly-trend').innerHTML = '<p class="text-center text-gray-400 text-sm py-12">{{ __("protege.no_records") }}</p>';
@endif
</script>
@endpush
