@extends('layouts.report')
@section('title', 'Company Performance Report — ' . $pelaksana->nama_syarikat)

@section('content')
<h2>Company Details</h2>
<div class="kpi-grid">
    <div class="kpi-card"><div class="value">{{ $pelaksana->kuota_digunakan }}/{{ $pelaksana->kuota_diluluskan }}</div><div class="label">Quota Used/Approved</div></div>
    <div class="kpi-card"><div class="value">RM {{ number_format($pelaksana->peruntukan_diluluskan,0) }}</div><div class="label">Budget Allocated</div></div>
    <div class="kpi-card"><div class="value">RM {{ number_format($pelaksana->peruntukan_diguna,0) }}</div><div class="label">Budget Used</div></div>
    <div class="kpi-card {{ $pelaksana->baki_peruntukan < 0 ? 'red' : 'green' }}"><div class="value">RM {{ number_format($pelaksana->baki_peruntukan,0) }}</div><div class="label">Balance</div></div>
</div>
<table>
    <tr><td><strong>Project</strong></td><td>{{ $pelaksana->projek_kontrak ?? '-' }}</td><td><strong>PIC</strong></td><td>{{ $pelaksana->pic_syarikat }}</td></tr>
    <tr><td><strong>Compliance</strong></td><td>{{ $pelaksana->tahap_pematuhan }}</td><td><strong>Fund Status</strong></td><td><span class="badge badge-{{ $pelaksana->status_dana === 'Mencukupi' ? 'green' : 'red' }}">{{ $pelaksana->status_dana }}</span></td></tr>
</table>

<h2>Graduate List ({{ $graduates->count() }})</h2>
<table>
    <thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Position</th><th>Status</th><th>Start</th><th>End</th></tr></thead>
    <tbody>
    @foreach($graduates as $g)
        <tr>
            <td>{{ $g->id_graduan ?? $g->talent_code }}</td>
            <td>{{ $g->full_name }}</td>
            <td>{{ $g->kategori ?? '-' }}</td>
            <td>{{ $g->jawatan ?? '-' }}</td>
            <td><span class="badge badge-{{ $g->status_aktif === 'Aktif' ? 'green' : 'gray' }}">{{ $g->status_aktif ?? $g->status }}</span></td>
            <td>{{ $g->tarikh_mula?->format('d/m/Y') ?? '-' }}</td>
            <td>{{ $g->tarikh_tamat?->format('d/m/Y') ?? '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Letter Status ({{ $suratRecords->count() }})</h2>
<table>
    <thead><tr><th>Type</th><th>Graduate</th><th>Status</th><th>PIC</th><th>Started</th><th>Completed</th></tr></thead>
    <tbody>
    @foreach($suratRecords as $s)
        <tr>
            <td>{{ $s->jenis_surat }}</td>
            <td>{{ $s->nama_graduan ?? $s->id_graduan }}</td>
            <td><span class="badge badge-{{ $s->status_surat === 'Selesai' ? 'green' : ($s->status_surat === 'Belum Mula' ? 'gray' : 'blue') }}">{{ $s->status_surat }}</span></td>
            <td>{{ $s->pic_responsible }}</td>
            <td>{{ $s->tarikh_mula_proses?->format('d/m/Y') ?? '-' }}</td>
            <td>{{ $s->tarikh_siap?->format('d/m/Y') ?? '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Payment Records ({{ $kewanganRecords->count() }})</h2>
<table>
    <thead><tr><th>Graduate</th><th>Month</th><th>Full (RM)</th><th>Pro-rate (RM)</th><th>Status</th><th>Days Late</th></tr></thead>
    <tbody>
    @foreach($kewanganRecords->take(30) as $k)
        <tr>
            <td>{{ $k->id_graduan }}</td>
            <td>{{ $k->bulan }}</td>
            <td>{{ number_format($k->elaun_penuh,2) }}</td>
            <td>{{ number_format($k->elaun_prorate,2) }}</td>
            <td><span class="badge badge-{{ $k->status_bayaran === 'Selesai' ? 'green' : ($k->status_bayaran === 'Lewat' ? 'red' : 'yellow') }}">{{ $k->status_bayaran }}</span></td>
            <td>{{ $k->hari_lewat }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
