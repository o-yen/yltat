@extends('layouts.admin')

@section('title', __('protege.kpi_title'))
@section('page-title', __('protege.kpi_title'))

@section('content')

<!-- Header -->
<div class="mb-6">
    @if($latest)
        <div class="bg-gradient-to-r from-[#1E3A5F] to-[#2d5a8e] rounded-xl p-6 text-white shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-200 text-sm">{{ __('protege.kpi_latest_month') }}</p>
                    <h2 class="text-3xl font-bold mt-1">{{ $latest->bulan }}</h2>
                </div>
                <div class="text-right">
                    <p class="text-blue-200 text-sm">{{ __('protege.total_records', ['count' => $records->count()]) }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl p-8 text-center text-gray-400 border border-gray-100">
            {{ __('protege.no_records') }}
        </div>
    @endif
</div>

@if($latest)
@php
    // Helper to build gradient card
    // Navy-based cohesive palette
    $navy = '#1E3A5F, #2d5a8e';      // Primary — navy blue
    $teal = '#0d9488, #2dd4bf';       // Good/positive — teal
    $amber = '#d97706, #f59e0b';      // Warning/attention — amber
    $rose = '#be123c, #f43f5e';       // Critical/negative — rose
    $indigo = '#4338ca, #6366f1';     // Info — indigo
    $emerald = '#047857, #10b981';    // Success — emerald
    $slate = '#334155, #64748b';      // Neutral — slate
    $violet = '#6d28d9, #8b5cf6';     // Special — violet

    $kpiCards = [
        // Row 1: Core metrics
        ['val' => number_format($latest->total_graduan_aktif), 'label' => __('protege.dash_graduan_aktif'), 'gradient' => $navy, 'link' => route('admin.talents.index'), 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['val' => number_format($latest->retention_rate_pct, 1) . '%', 'label' => 'Retention Rate', 'target' => ($targets['retention_rate_pct'] ?? 75) . '%', 'met' => ($latest->retention_rate_pct ?? 0) >= 75, 'gradient' => $emerald, 'link' => route('admin.talents.index', ['status' => 'Diserap']), 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
        ['val' => number_format($latest->total_bayaran_selesai), 'label' => __('protege.bayaran_selesai'), 'gradient' => $teal, 'link' => route('admin.kewangan.index', ['status_bayaran' => 'Selesai']), 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['val' => number_format($latest->total_bayaran_lewat), 'label' => __('protege.bayaran_lewat'), 'gradient' => $rose, 'link' => route('admin.kewangan.index', ['status_bayaran' => 'Lewat']), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        // Row 2: Performance
        ['val' => number_format(($latest->avg_kehadiran_pct ?? 0) * 100, 1) . '%', 'label' => __('protege.kh_kehadiran_pct'), 'target' => '85%', 'met' => ($latest->avg_kehadiran_pct ?? 0) >= 0.85, 'gradient' => $indigo, 'link' => route('admin.kehadiran.index'), 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['val' => number_format($latest->avg_prestasi_score ?? 0, 1), 'label' => __('protege.kh_skor_prestasi'), 'target' => '7.5', 'met' => ($latest->avg_prestasi_score ?? 0) >= 7.5, 'gradient' => $violet, 'link' => route('admin.kehadiran.index'), 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
        ['val' => number_format($latest->surat_kuning_siap_pct ?? 0, 1) . '%', 'label' => __('protege.dash_surat_kuning_siap'), 'target' => '90%', 'met' => ($latest->surat_kuning_siap_pct ?? 0) >= 90, 'gradient' => $amber, 'link' => route('admin.status-surat.index', ['jenis_surat' => 'Surat Kuning']), 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['val' => number_format($latest->surat_biru_siap_pct ?? 0, 1) . '%', 'label' => __('protege.dash_surat_biru_siap'), 'target' => '85%', 'met' => ($latest->surat_biru_siap_pct ?? 0) >= 85, 'gradient' => $navy, 'link' => route('admin.status-surat.index', ['jenis_surat' => 'Surat Biru']), 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        // Row 3: Compliance
        ['val' => number_format($latest->logbook_submitted_pct ?? 0, 1) . '%', 'label' => 'Logbook %', 'target' => '90%', 'met' => ($latest->logbook_submitted_pct ?? 0) >= 90, 'gradient' => $teal, 'link' => route('admin.logbook.index'), 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['val' => $latest->isu_kritikal_active ?? 0, 'label' => __('protege.isu_kritikal_aktif'), 'gradient' => $rose, 'link' => route('admin.isu-risiko.index', ['tahap_risiko' => 'Kritikal']), 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ['val' => number_format($latest->budget_utilization_pct ?? 0, 1) . '%', 'label' => 'Budget Utilization', 'target' => '80-95%', 'met' => ($latest->budget_utilization_pct ?? 0) >= 80 && ($latest->budget_utilization_pct ?? 0) <= 95, 'gradient' => $emerald, 'link' => route('admin.syarikat-pelaksana.index'), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['val' => number_format($latest->training_compliance_rate_pct ?? 0, 1) . '%', 'label' => __('protege.spen_compliance'), 'target' => '85%', 'met' => ($latest->training_compliance_rate_pct ?? 0) >= 85, 'gradient' => $indigo, 'link' => route('admin.syarikat-penempatan.index'), 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
        // Row 4: Training
        ['val' => $latest->training_sessions_completed ?? 0, 'label' => __('protege.dash_sesi_latihan'), 'gradient' => $slate, 'link' => route('admin.training.index'), 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
        ['val' => number_format($latest->avg_training_satisfaction ?? 0, 1) . '/10', 'label' => __('protege.trn_kepuasan'), 'target' => '8.5/10', 'met' => ($latest->avg_training_satisfaction ?? 0) >= 8.5, 'gradient' => $violet, 'link' => route('admin.training.index', ['status' => 'Selesai']), 'icon' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['val' => number_format($latest->avg_skill_improvement_pct ?? 0, 1) . '%', 'label' => __('protege.trn_improvement'), 'target' => '35%', 'met' => ($latest->avg_skill_improvement_pct ?? 0) >= 35, 'gradient' => $emerald, 'link' => route('admin.training.index'), 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
    ];
@endphp

<!-- KPI Cards Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($kpiCards as $card)
    <a href="{{ $card['link'] ?? '#' }}" class="relative overflow-hidden rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5 block"
       style="background: linear-gradient(135deg, {{ $card['gradient'] }});">
        <div class="absolute top-0 right-0 w-24 h-24 -mr-6 -mt-6 rounded-full opacity-15" style="background: rgba(255,255,255,0.3);"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                </div>
                @if(isset($card['target']))
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($card['met'] ?? true) ? 'bg-white/30 text-white' : 'bg-white/90 text-red-600' }}">
                        {{ __('protege.kpi_target') }}: {{ $card['target'] }}
                    </span>
                @endif
            </div>
            <div class="text-2xl font-bold text-white">{{ $card['val'] }}</div>
            <div class="text-white/70 text-xs mt-1">{{ $card['label'] }}</div>
        </div>
    </a>
    @endforeach
</div>

<!-- Retention Rate Trend Chart -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">Retention Rate Trend</h3>
    <div id="retentionChart"></div>
</div>

<!-- Historical Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">KPI History</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase sticky left-0 bg-gray-50">{{ __('protege.bulan') }}</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500">{{ __('protege.dash_graduan_aktif') }}</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500">Retention %</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500">{{ __('protege.kh_kehadiran_pct') }}</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500">{{ __('protege.kh_skor_prestasi') }}</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500">{{ __('protege.surat_kuning') }} %</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500">{{ __('protege.surat_biru') }} %</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500">Logbook %</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500">Budget %</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500">{{ __('protege.spen_compliance') }} %</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($records as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800 sticky left-0 bg-white">{{ $r->bulan }}</td>
                        <td class="px-3 py-3 text-center">{{ $r->total_graduan_aktif }}</td>
                        <td class="px-3 py-3 text-center"><span class="px-2 py-0.5 rounded text-xs font-medium {{ $r->retention_rate_pct >= 75 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">{{ number_format($r->retention_rate_pct, 1) }}%</span></td>
                        <td class="px-3 py-3 text-center">{{ number_format($r->avg_kehadiran_pct * 100, 1) }}%</td>
                        <td class="px-3 py-3 text-center">{{ number_format($r->avg_prestasi_score, 1) }}</td>
                        <td class="px-3 py-3 text-center">{{ number_format($r->surat_kuning_siap_pct, 1) }}%</td>
                        <td class="px-3 py-3 text-center">{{ number_format($r->surat_biru_siap_pct, 1) }}%</td>
                        <td class="px-3 py-3 text-center">{{ number_format($r->logbook_submitted_pct, 1) }}%</td>
                        <td class="px-3 py-3 text-center"><span class="px-2 py-0.5 rounded text-xs font-medium {{ ($r->budget_utilization_pct >= 80 && $r->budget_utilization_pct <= 95) ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">{{ number_format($r->budget_utilization_pct, 1) }}%</span></td>
                        <td class="px-3 py-3 text-center">{{ number_format($r->training_compliance_rate_pct, 1) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@push('scripts')
<script>
@if($records->count() > 1)
    var options = {
        chart: { type: 'line', height: 300, toolbar: { show: false } },
        series: [{
            name: 'Retention Rate %',
            data: @json($records->pluck('retention_rate_pct')->values())
        }],
        xaxis: {
            categories: @json($records->map(fn($r) => $r->bulan . ' ' . $r->tahun)->values())
        },
        yaxis: { min: 0, max: 100, labels: { formatter: v => v.toFixed(0) + '%' } },
        colors: ['#1E3A5F'],
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 5 },
        annotations: {
            yaxis: [{ y: {{ $targets['retention_rate_pct'] ?? 75 }}, borderColor: '#10B981', label: { text: 'Target: {{ $targets["retention_rate_pct"] ?? 75 }}%', style: { color: '#10B981' } } }]
        }
    };
    new ApexCharts(document.querySelector("#retentionChart"), options).render();
@endif
</script>
@endpush
@endsection
