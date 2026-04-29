@extends('layouts.admin')
@section('title', __('nav.reports'))
@section('page-title', __('nav.reports'))

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- 1. Monthly Executive Report --}}
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 rounded-full opacity-10" style="background: white;"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="text-white text-lg font-bold">Monthly Executive Report</h3>
            <p class="text-white/70 text-sm mt-1 mb-4">KPI, trend, budget, isu kritikal, rekomendasi</p>
            <div class="flex items-center gap-2 text-white/60 text-xs mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857"/></svg>
                Audience: PMO, MINDEF
            </div>
            <a href="{{ route('admin.reports.export.executive') }}" target="_blank"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-[#667eea] rounded-lg text-sm font-semibold hover:bg-white/90 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Generate PDF
            </a>
        </div>
    </div>

    {{-- 2. Company Performance Report --}}
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);">
        <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 rounded-full opacity-10" style="background: white;"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h3 class="text-white text-lg font-bold">Company Performance Report</h3>
            <p class="text-white/70 text-sm mt-1 mb-4">Senarai peserta, bayaran, surat, tindakan</p>
            <div class="flex items-center gap-2 text-white/60 text-xs mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                Audience: Syarikat Pelaksana
            </div>
            <form method="GET" action="{{ route('admin.reports.export.company') }}" target="_blank" class="flex items-center gap-2">
                <select name="id_pelaksana" class="px-3 py-2 rounded-lg text-sm bg-white/20 text-white border border-white/30 focus:outline-none">
                    @foreach(\App\Models\SyarikatPelaksana::all() as $sp)
                        <option value="{{ $sp->id_pelaksana }}" class="text-gray-800">{{ $sp->nama_syarikat }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-white text-orange-600 rounded-lg text-sm font-semibold hover:bg-white/90 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Generate PDF
                </button>
            </form>
        </div>
    </div>

    {{-- 3. Participant Progress Report --}}
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
        <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 rounded-full opacity-10" style="background: white;"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h3 class="text-white text-lg font-bold">Participant Progress Report</h3>
            <p class="text-white/70 text-sm mt-1 mb-4">Attendance, prestasi, logbook, komen</p>
            <div class="flex items-center gap-2 text-white/60 text-xs mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Audience: Mentor / Partner
            </div>
            <form method="GET" action="{{ route('admin.reports.export.participant') }}" target="_blank" class="flex items-center gap-2">
                <select name="talent_id" class="px-3 py-2 rounded-lg text-sm bg-white/20 text-white border border-white/30 focus:outline-none max-w-[200px]">
                    @foreach(\App\Models\Talent::whereNotNull('id_graduan')->orderBy('full_name')->limit(50)->get() as $t)
                        <option value="{{ $t->id }}" class="text-gray-800">{{ $t->id_graduan }} — {{ Str::limit($t->full_name, 25) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-white text-cyan-600 rounded-lg text-sm font-semibold hover:bg-white/90 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Generate PDF
                </button>
            </form>
        </div>
    </div>

    {{-- 4. Training Effectiveness Report --}}
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
        <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 rounded-full opacity-10" style="background: white;"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <h3 class="text-white text-lg font-bold">Training Effectiveness Report</h3>
            <p class="text-white/70 text-sm mt-1 mb-4">Participation, outcomes, ROI, correlation, action plan</p>
            <div class="flex items-center gap-2 text-white/60 text-xs mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547"/></svg>
                Audience: PMO / MINDEF / Partner
            </div>
            <a href="{{ route('admin.reports.export.training') }}" target="_blank"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-green-600 rounded-lg text-sm font-semibold hover:bg-white/90 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Generate PDF
            </a>
        </div>
    </div>

</div>

{{-- Instructions --}}
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-5">
    <h4 class="text-sm font-semibold text-blue-800 mb-2">How to save as PDF</h4>
    <ol class="text-sm text-blue-700 list-decimal ml-4 space-y-1">
        <li>Click <strong>Generate PDF</strong> to open the report preview</li>
        <li>Press <strong>Ctrl+P</strong> (Windows) or <strong>Cmd+P</strong> (Mac)</li>
        <li>Select <strong>"Save as PDF"</strong> as the destination</li>
        <li>Click <strong>Save</strong></li>
    </ol>
</div>

@endsection
