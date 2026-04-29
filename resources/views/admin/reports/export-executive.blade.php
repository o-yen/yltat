@extends('layouts.report')
@section('title', 'Monthly Executive Report')

@section('content')
<h2>KPI Summary</h2>
@if($latestKpi)
<p style="margin-bottom:10px;color:#666;">Period: {{ $latestKpi->bulan }}</p>
<div class="kpi-grid">
    <div class="kpi-card"><div class="value">{{ $totalAktif }}</div><div class="label">Active Graduates</div></div>
    <div class="kpi-card {{ $latestKpi->retention_rate_pct >= 75 ? 'green' : 'red' }}"><div class="value">{{ number_format($latestKpi->retention_rate_pct,1) }}%</div><div class="label">Retention Rate (Target >75%)</div></div>
    <div class="kpi-card {{ ($latestKpi->avg_kehadiran_pct*100) >= 85 ? 'green' : 'red' }}"><div class="value">{{ number_format($latestKpi->avg_kehadiran_pct*100,1) }}%</div><div class="label">Avg Attendance (Target >85%)</div></div>
    <div class="kpi-card {{ $latestKpi->avg_prestasi_score >= 7.5 ? 'green' : 'red' }}"><div class="value">{{ number_format($latestKpi->avg_prestasi_score,1) }}</div><div class="label">Avg Performance (Target >7.5)</div></div>
</div>
<div class="kpi-grid">
    <div class="kpi-card"><div class="value">{{ number_format($latestKpi->surat_kuning_siap_pct,1) }}%</div><div class="label">Yellow Letter Done</div></div>
    <div class="kpi-card"><div class="value">{{ number_format($latestKpi->surat_biru_siap_pct,1) }}%</div><div class="label">Blue Letter Done</div></div>
    <div class="kpi-card"><div class="value">{{ number_format($latestKpi->logbook_submitted_pct,1) }}%</div><div class="label">Logbook Submitted</div></div>
    <div class="kpi-card {{ $latestKpi->budget_utilization_pct >= 80 && $latestKpi->budget_utilization_pct <= 95 ? 'green' : 'yellow' }}"><div class="value">{{ number_format($latestKpi->budget_utilization_pct,1) }}%</div><div class="label">Budget Utilization (80-95%)</div></div>
</div>
@endif

<h2>Implementing Companies</h2>
<table>
    <thead><tr><th>Company</th><th>Quota</th><th>Budget (RM)</th><th>Used (RM)</th><th>Balance (RM)</th><th>Fund Status</th></tr></thead>
    <tbody>
    @foreach($pelaksana as $sp)
        <tr>
            <td><strong>{{ $sp->nama_syarikat }}</strong></td>
            <td>{{ $sp->kuota_digunakan }}/{{ $sp->kuota_diluluskan }}</td>
            <td>{{ number_format($sp->peruntukan_diluluskan,0) }}</td>
            <td>{{ number_format($sp->peruntukan_diguna,0) }}</td>
            <td>{{ number_format($sp->baki_peruntukan,0) }}</td>
            <td><span class="badge badge-{{ $sp->status_dana === 'Mencukupi' ? 'green' : ($sp->status_dana === 'Kritikal' ? 'red' : 'yellow') }}">{{ $sp->status_dana }}</span></td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Active Issues & Risks</h2>
<table>
    <thead><tr><th>ID</th><th>Date</th><th>Category</th><th>Risk</th><th>Status</th><th>PIC</th><th>Details</th></tr></thead>
    <tbody>
    @foreach($activeIssues as $isu)
        <tr>
            <td>{{ $isu->id_isu }}</td>
            <td>{{ $isu->tarikh_isu?->format('d/m/Y') }}</td>
            <td>{{ $isu->kategori_isu }}</td>
            <td><span class="badge badge-{{ $isu->tahap_risiko === 'Kritikal' ? 'red' : ($isu->tahap_risiko === 'Tinggi' ? 'yellow' : 'green') }}">{{ $isu->tahap_risiko }}</span></td>
            <td><span class="badge badge-blue">{{ $isu->status }}</span></td>
            <td>{{ $isu->pic }}</td>
            <td>{{ Str::limit($isu->butiran_isu, 50) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@if($kpiHistory->count() > 1)
<h2>KPI Trend</h2>
<table>
    <thead><tr><th>Month</th><th>Graduates</th><th>Retention</th><th>Attendance</th><th>Performance</th><th>Budget</th><th>Training</th></tr></thead>
    <tbody>
    @foreach($kpiHistory as $kpi)
        <tr>
            <td>{{ $kpi->bulan }}</td>
            <td>{{ $kpi->total_graduan_aktif }}</td>
            <td>{{ number_format($kpi->retention_rate_pct,1) }}%</td>
            <td>{{ number_format($kpi->avg_kehadiran_pct*100,1) }}%</td>
            <td>{{ number_format($kpi->avg_prestasi_score,1) }}</td>
            <td>{{ number_format($kpi->budget_utilization_pct,1) }}%</td>
            <td>{{ number_format($kpi->training_compliance_rate_pct,1) }}%</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif
@endsection
