@extends('layouts.report')
@section('title', 'Training Effectiveness Report')

@section('content')
<h2>Training Summary</h2>
<div class="kpi-grid">
    <div class="kpi-card"><div class="value">{{ $records->count() }}</div><div class="label">Total Sessions</div></div>
    <div class="kpi-card"><div class="value">{{ $records->where('status', 'Selesai')->count() }}</div><div class="label">Completed</div></div>
    <div class="kpi-card green"><div class="value">{{ number_format($avgSatisfaction ?? 0, 1) }}/10</div><div class="label">Avg Satisfaction</div></div>
    <div class="kpi-card green"><div class="value">{{ number_format($avgImprovement ?? 0, 1) }}%</div><div class="label">Avg Improvement</div></div>
</div>
<div class="kpi-grid" style="grid-template-columns: repeat(2, 1fr);">
    <div class="kpi-card"><div class="value">RM {{ number_format($totalBudget ?? 0, 0) }}</div><div class="label">Budget Allocated</div></div>
    <div class="kpi-card"><div class="value">RM {{ number_format($totalSpent ?? 0, 0) }}</div><div class="label">Budget Spent</div></div>
</div>

<h2>Training Sessions</h2>
<table>
    <thead><tr><th>ID</th><th>Company</th><th>Title</th><th>Session</th><th>Date</th><th>Attended</th><th>Pre</th><th>Post</th><th>Improve</th><th>Satisfaction</th><th>Status</th></tr></thead>
    <tbody>
    @foreach($records as $tr)
        <tr>
            <td>{{ $tr->id_training }}</td>
            <td>{{ Str::limit($tr->nama_syarikat, 20) }}</td>
            <td>{{ Str::limit($tr->tajuk_training, 25) }}</td>
            <td>{{ $tr->sesi }}</td>
            <td>{{ $tr->tarikh_training?->format('d/m/Y') }}</td>
            <td>{{ $tr->jumlah_hadir }}/{{ $tr->jumlah_dijemput }}</td>
            <td>{{ $tr->pre_assessment_avg }}</td>
            <td>{{ $tr->post_assessment_avg }}</td>
            <td>{{ number_format($tr->improvement_pct, 1) }}%</td>
            <td>{{ $tr->skor_kepuasan }}/10</td>
            <td><span class="badge badge-{{ $tr->status === 'Selesai' ? 'green' : ($tr->status === 'Dirancang' ? 'yellow' : 'blue') }}">{{ $tr->status }}</span></td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Participant Outcomes ({{ $participants->count() }} participants)</h2>
<table>
    <thead><tr><th>Metric</th><th>Value</th></tr></thead>
    <tbody>
        <tr><td>Total Participants</td><td>{{ $participants->count() }}</td></tr>
        <tr><td>Attended</td><td>{{ $participants->where('status_kehadiran', 'Hadir')->count() }}</td></tr>
        <tr><td>Certificates Issued</td><td>{{ $participants->where('certificate_issued', true)->count() }}</td></tr>
        <tr><td>Feedback Submitted</td><td>{{ $participants->where('feedback_submitted', true)->count() }}</td></tr>
        <tr><td>Action Plans Submitted</td><td>{{ $participants->where('action_plan_submitted', true)->count() }}</td></tr>
        <tr><td>Avg Pre-Assessment</td><td>{{ number_format($participants->avg('pre_assessment_score'), 1) }}</td></tr>
        <tr><td>Avg Post-Assessment</td><td>{{ number_format($participants->avg('post_assessment_score'), 1) }}</td></tr>
        <tr><td>Avg Improvement</td><td>{{ number_format($participants->avg('improvement_pct'), 1) }}%</td></tr>
    </tbody>
</table>
@endsection
