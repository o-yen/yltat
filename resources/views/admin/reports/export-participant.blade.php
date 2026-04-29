@extends('layouts.report')
@section('title', 'Participant Progress Report — ' . $talent->full_name)

@section('content')
<h2>Participant Profile</h2>
<table>
    <tr><td width="25%"><strong>Name</strong></td><td>{{ $talent->full_name }}</td><td width="25%"><strong>ID</strong></td><td>{{ $talent->id_graduan ?? $talent->talent_code }}</td></tr>
    <tr><td><strong>Category</strong></td><td>{{ $talent->kategori ?? '-' }}</td><td><strong>IC No.</strong></td><td>{{ $talent->ic_passport_no }}</td></tr>
    <tr><td><strong>University</strong></td><td>{{ $talent->university ?? '-' }}</td><td><strong>Programme</strong></td><td>{{ $talent->programme ?? '-' }}</td></tr>
    <tr><td><strong>Implementing Co.</strong></td><td>{{ $talent->syarikatPelaksana?->nama_syarikat ?? '-' }}</td><td><strong>Placement Co.</strong></td><td>{{ $talent->syarikatPenempatan?->nama_syarikat ?? '-' }}</td></tr>
    <tr><td><strong>Position</strong></td><td>{{ $talent->jawatan ?? '-' }}</td><td><strong>Status</strong></td><td><span class="badge badge-{{ ($talent->status_aktif ?? '') === 'Aktif' ? 'green' : 'gray' }}">{{ $talent->status_aktif ?? $talent->status }}</span></td></tr>
    <tr><td><strong>Start Date</strong></td><td>{{ $talent->tarikh_mula?->format('d/m/Y') ?? '-' }}</td><td><strong>End Date</strong></td><td>{{ $talent->tarikh_tamat?->format('d/m/Y') ?? '-' }}</td></tr>
    <tr><td><strong>6-Month Absorption</strong></td><td colspan="3">{{ $talent->status_penyerapan_6bulan ?? '-' }}</td></tr>
</table>

<h2>Monthly Attendance & Performance</h2>
<table>
    <thead><tr><th>Month</th><th>Attendance %</th><th>Days Present</th><th>Working Days</th><th>Performance (1-10)</th><th>Mentor Comment</th><th>Logbook</th></tr></thead>
    <tbody>
    @forelse($kehadiran as $kh)
        <tr>
            <td>{{ $kh->bulan }}</td>
            <td><span class="badge badge-{{ $kh->kehadiran_pct >= 0.85 ? 'green' : ($kh->kehadiran_pct >= 0.75 ? 'yellow' : 'red') }}">{{ number_format($kh->kehadiran_pct * 100, 0) }}%</span></td>
            <td>{{ $kh->hari_hadir }}</td>
            <td>{{ $kh->hari_bekerja }}</td>
            <td><span class="badge badge-{{ $kh->skor_prestasi >= 8 ? 'green' : ($kh->skor_prestasi >= 6 ? 'blue' : 'red') }}">{{ $kh->skor_prestasi }}/10</span></td>
            <td>{{ $kh->komen_mentor ?? '-' }}</td>
            <td><span class="badge badge-{{ $kh->status_logbook === 'Dikemukakan' ? 'green' : ($kh->status_logbook === 'Lewat' ? 'yellow' : 'red') }}">{{ $kh->status_logbook }}</span></td>
        </tr>
    @empty
        <tr><td colspan="7" style="text-align:center;color:#999;">No records</td></tr>
    @endforelse
    </tbody>
</table>

<h2>Logbook Submissions</h2>
<table>
    <thead><tr><th>Month</th><th>Status</th><th>Upload Date</th><th>Review Status</th><th>Mentor</th><th>Comment</th></tr></thead>
    <tbody>
    @forelse($logbooks as $lb)
        <tr>
            <td>{{ $lb->bulan }}</td>
            <td><span class="badge badge-{{ $lb->status_logbook === 'Dikemukakan' ? 'green' : ($lb->status_logbook === 'Lewat' ? 'yellow' : 'red') }}">{{ $lb->status_logbook }}</span></td>
            <td>{{ $lb->tarikh_upload?->format('d/m/Y') ?? '-' }}</td>
            <td><span class="badge badge-{{ $lb->status_semakan === 'Lulus' ? 'green' : 'gray' }}">{{ $lb->status_semakan }}</span></td>
            <td>{{ $lb->nama_mentor ?? '-' }}</td>
            <td>{{ $lb->komen_mentor ?? '-' }}</td>
        </tr>
    @empty
        <tr><td colspan="6" style="text-align:center;color:#999;">No records</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
