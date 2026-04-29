@extends('layouts.admin')

@section('title', $talent->full_name)
@section('page-title', __('common.talent_profile'))

@section('content')
<div class="mb-5 flex items-center justify-between">
    <a href="{{ route('admin.talents.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('common.back_to_list') }}
    </a>
    @if(in_array(\App\Http\Middleware\ModuleAccess::levelFor(auth()->user()->role?->role_name, 'talents'), ['full', 'edit', 'own', 'create']))
    <a href="{{ route('admin.talents.edit', $talent) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        {{ __('common.edit_profile') }}
    </a>
    @endif
</div>

<!-- Profile Header -->
<div class="bg-gradient-to-r from-[#1E3A5F] to-[#2d5a8e] rounded-xl p-6 text-white mb-6 shadow-md">
    <div class="flex items-start gap-5">
        @if($talent->profile_photo)
            <img src="{{ asset("storage/{$talent->profile_photo}") }}" alt="{{ $talent->full_name }}"
                 class="w-16 h-16 rounded-2xl object-cover flex-shrink-0 border-2 border-white/30">
        @else
            <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-2xl font-bold flex-shrink-0">
                {{ substr($talent->full_name, 0, 1) }}
            </div>
        @endif
        <div class="flex-1">
            <h2 class="text-2xl font-bold">{{ $talent->full_name }}</h2>
            <div class="text-blue-200 font-mono text-sm mt-1">{{ $talent->id_graduan ?? $talent->talent_code }}</div>
            <div class="flex flex-wrap gap-3 mt-3">
                @include('partials.talent-status-badge', ['status' => $talent->resolved_status])
                @if($talent->university)
                    <span class="text-sm text-blue-100">{{ $talent->university }}</span>
                @endif
                @if($talent->programme)
                    <span class="text-sm text-blue-200">• {{ $talent->programme }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<div x-data="{ tab: 'info' }">
    <div class="flex gap-1 mb-5 bg-gray-100 p-1 rounded-xl w-fit overflow-x-auto">
        @foreach([
            ['info', __('common.tab_personal')],
            ['protege', __('common.tab_protege')],
            ['monitoring', 'Monitoring'],
            ['background', __('common.tab_background')],
            ['feedback', __('nav.feedback')],
            ['finance', __('common.tab_finance')],
            ['documents', __('common.tab_documents_certs')],
            ['notes', __('common.tab_admin_notes')],
        ] as [$tabId, $tabLabel])
            <button type="button" @click="tab = '{{ $tabId }}'"
                    :class="tab === '{{ $tabId }}' ? 'bg-white shadow-sm text-[#1E3A5F]' : 'text-gray-500'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap">
                {{ $tabLabel }}
            </button>
        @endforeach
    </div>

    <!-- Personal Info Tab -->
    <div x-show="tab === 'info'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach([
                [__('protege.id_graduan'), $talent->id_graduan ?? $talent->talent_code],
                [__('common.ic_passport_no'), $talent->ic_passport_no],
                [__('common.date_of_birth'), $talent->date_of_birth?->format('d/m/Y')],
                [__('portal.gender'), $talent->gender ? __('common.gender.' . $talent->gender) : null],
                [__('protege.status'), $talent->resolved_status ? __('common.status.' . $talent->resolved_status) : null],
                [__('common.email'), $talent->email],
                [__('common.phone_no'), $talent->phone],
                [__('common.state'), $talent->negeri],
                [__('common.qualification'), $talent->kelayakan],
                [__('common.university'), $talent->university],
                [__('common.programme'), $talent->programme],
                [__('common.cgpa'), $talent->cgpa],
                [__('common.graduation_year'), $talent->graduation_year],
                [__('common.start_date'), $talent->tarikh_mula?->format('d/m/Y')],
                [__('common.end_date'), $talent->tarikh_tamat?->format('d/m/Y')],
            ] as [$label, $value])
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</label>
                    <p class="text-gray-800 mt-1">{{ $value ?? '-' }}</p>
                </div>
            @endforeach

            <div class="md:col-span-2">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.address') }}</label>
                <p class="text-gray-800 mt-1">{{ $talent->address ?? '-' }}</p>
            </div>

            @if($talent->profile_summary)
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.profile_summary') }}</label>
                    <p class="text-gray-700 mt-1 leading-relaxed">{{ $talent->profile_summary }}</p>
                </div>
            @endif

            @if($talent->skills_text)
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('portal.skills') }}</label>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach(explode(',', $talent->skills_text) as $skill)
                            @if(trim($skill))
                                <span class="inline-block bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">{{ trim($skill) }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- PROTEGE Programme Tab -->
    <div x-show="tab === 'protege'" style="display:none">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('common.protege_programme') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.category') }}</label>
                    <p class="text-gray-800 mt-1">
                        @if($talent->kategori)
                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700">{{ $talent->kategori }}</span>
                        @else - @endif
                    </p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.six_month_absorption_status') }}</label>
                    <p class="text-gray-800 mt-1">
                        @if($talent->status_penyerapan_6bulan)
                            @php
                                $abColor = match($talent->status_penyerapan_6bulan) {
                                    'Diserap' => 'bg-green-50 text-green-700',
                                    'Tidak Diserap' => 'bg-red-50 text-red-700',
                                    default => 'bg-yellow-50 text-yellow-700',
                                };
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium {{ $abColor }}">{{ $talent->status_penyerapan_6bulan }}</span>
                        @else - @endif
                    </p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.implementing_company') }}</label>
                    <p class="text-gray-800 mt-1">
                        @if($talent->syarikatPelaksana)
                            <span class="font-mono text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ $talent->id_pelaksana }}</span>
                            {{ $talent->syarikatPelaksana->nama_syarikat }}
                        @else - @endif
                    </p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.placement_company') }}</label>
                    <p class="text-gray-800 mt-1">
                        @if($talent->syarikatPenempatan)
                            <span class="font-mono text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ $talent->id_syarikat_penempatan }}</span>
                            {{ $talent->syarikatPenempatan->nama_syarikat }}
                        @else - @endif
                    </p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.job_title') }}</label>
                    <p class="text-gray-800 mt-1">{{ $talent->jawatan ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.current_programme_status') }}</label>
                    <p class="text-gray-800 mt-1">
                        @if($talent->status_aktif)
                            @include('partials.talent-status-badge', ['status' => $talent->status_aktif])
                        @else - @endif
                    </p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.start_date') }}</label>
                    <p class="text-gray-800 mt-1">{{ $talent->tarikh_mula?->format('d/m/Y') ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.end_date') }}</label>
                    <p class="text-gray-800 mt-1">{{ $talent->tarikh_tamat?->format('d/m/Y') ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Placement Details --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-5">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('common.placement_details') ?? 'Placement Details' }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach([
                    [__('common.department') ?? 'Department', $talent->department],
                    [__('common.programme_type') ?? 'Programme Type', $talent->programme_type],
                    [__('common.supervisor_name') ?? 'Supervisor', $talent->supervisor_name],
                    [__('common.supervisor_email') ?? 'Supervisor Email', $talent->supervisor_email],
                    [__('common.duration_months') ?? 'Duration', $talent->duration_months ? $talent->duration_months . ' months' : null],
                    [__('common.monthly_stipend') ?? 'Monthly Stipend', $talent->monthly_stipend ? 'RM ' . number_format($talent->monthly_stipend, 2) : null],
                    [__('common.additional_cost') ?? 'Additional Cost', $talent->additional_cost ? 'RM ' . number_format($talent->additional_cost, 2) : null],
                ] as [$label, $value])
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</label>
                        <p class="text-gray-800 mt-1">{{ $value ?? '-' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Monitoring Tab -->
    <div x-show="tab === 'monitoring'" style="display:none">
        <div class="space-y-5">

            {{-- Surat Kuning & Biru Status --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    {{ __('protege.ss_title') }}
                    <span class="ml-auto text-xs text-gray-400">{{ ($suratRecords ?? collect())->count() }} {{ __('common.total') }}</span>
                </h3>
                @if(($suratRecords ?? collect())->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50"><tr>
                            <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.ss_jenis_surat') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.status') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.pic') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.ss_tarikh_mula') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.ss_tarikh_siap') }}</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($suratRecords as $s)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2"><span class="text-xs px-2 py-0.5 rounded {{ $s->jenis_surat === 'Surat Kuning' ? 'bg-yellow-50 text-yellow-700' : 'bg-blue-50 text-blue-700' }}">{{ $s->jenis_surat }}</span></td>
                                <td class="px-3 py-2 text-center">
                                    @php $stC = match($s->status_surat) { 'Selesai' => 'bg-green-100 text-green-700', 'Hantar','Tandatangan' => 'bg-blue-100 text-blue-700', 'Semakan','Draft' => 'bg-yellow-100 text-yellow-700', default => 'bg-gray-100 text-gray-500' }; @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $stC }}">{{ $s->status_surat }}</span>
                                </td>
                                <td class="px-3 py-2 text-center text-xs text-gray-600">{{ $s->pic_responsible }}</td>
                                <td class="px-3 py-2 text-center text-xs text-gray-500">{{ $s->tarikh_mula_proses?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-3 py-2 text-center text-xs text-gray-500">{{ $s->tarikh_siap?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-gray-400 text-sm py-4">{{ __('protege.no_records') }}</p>
                @endif
            </div>

            {{-- Kehadiran & Prestasi --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ __('protege.kh_title') }}
                    <span class="ml-auto text-xs text-gray-400">{{ ($kehadiranRecords ?? collect())->count() }} {{ __('common.total') }}</span>
                </h3>
                @if(($kehadiranRecords ?? collect())->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50"><tr>
                            <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.bulan') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.kh_kehadiran_pct') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.kh_hari_hadir') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.kh_skor_prestasi') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.kh_status_logbook') }}</th>
                            <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.kh_komen_mentor') }}</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($kehadiranRecords as $kh)
                            @php
                                $khPct = round($kh->kehadiran_pct * 100);
                                $khC = $khPct >= 85 ? 'green' : ($khPct >= 75 ? 'yellow' : 'red');
                                $skC = $kh->skor_prestasi >= 8 ? 'green' : ($kh->skor_prestasi >= 6 ? 'blue' : 'red');
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 text-gray-700 font-medium text-xs">{{ $kh->bulan }}</td>
                                <td class="px-3 py-2 text-center"><span class="text-xs font-semibold text-{{ $khC }}-600">{{ $khPct }}%</span></td>
                                <td class="px-3 py-2 text-center text-xs text-gray-600">{{ $kh->hari_hadir }}/{{ $kh->hari_bekerja }}</td>
                                <td class="px-3 py-2 text-center"><span class="text-xs font-semibold text-{{ $skC }}-600">{{ $kh->skor_prestasi }}/10</span></td>
                                <td class="px-3 py-2 text-center">
                                    @php $lbC = match($kh->status_logbook) { 'Dikemukakan' => 'bg-green-100 text-green-700', 'Lewat' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' }; @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $lbC }}">{{ $kh->status_logbook }}</span>
                                </td>
                                <td class="px-3 py-2 text-xs text-gray-500">{{ Str::limit($kh->komen_mentor, 30) ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-gray-400 text-sm py-4">{{ __('protege.no_records') }}</p>
                @endif
            </div>

            {{-- Logbook Submissions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    {{ __('protege.log_title') }}
                    <span class="ml-auto text-xs text-gray-400">{{ ($logbookRecords ?? collect())->count() }} {{ __('common.total') }}</span>
                </h3>
                @if(($logbookRecords ?? collect())->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50"><tr>
                            <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.bulan') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.log_status_logbook') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.log_status_semakan') }}</th>
                            <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.log_nama_mentor') }}</th>
                            <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.log_komen_mentor') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.log_link_file') }}</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($logbookRecords as $lb)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 text-gray-700 font-medium text-xs">{{ $lb->bulan }}</td>
                                <td class="px-3 py-2 text-center">
                                    @php $lbSt = match($lb->status_logbook) { 'Dikemukakan','Dalam Semakan' => 'bg-green-100 text-green-700', 'Lewat' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' }; @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $lbSt }}">{{ $lb->status_logbook }}</span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @php $smC = match($lb->status_semakan) { 'Lulus' => 'bg-green-100 text-green-700', 'Dalam Proses' => 'bg-blue-100 text-blue-700', 'Perlu Semakan Semula' => 'bg-yellow-100 text-yellow-700', default => 'bg-gray-100 text-gray-500' }; @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $smC }}">{{ $lb->status_semakan }}</span>
                                </td>
                                <td class="px-3 py-2 text-xs text-gray-600">{{ $lb->nama_mentor ?? '-' }}</td>
                                <td class="px-3 py-2 text-xs text-gray-500">{{ Str::limit($lb->komen_mentor, 30) ?? '-' }}</td>
                                <td class="px-3 py-2 text-center">
                                    @if($lb->link_file_logbook)
                                    <a href="{{ $lb->link_file_logbook }}" target="_blank" class="text-blue-600 hover:underline text-xs">View</a>
                                    @else <span class="text-gray-400 text-xs">-</span> @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-gray-400 text-sm py-4">{{ __('protege.no_records') }}</p>
                @endif
            </div>

            {{-- Kewangan Elaun --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('protege.kw_title') }}
                    <span class="ml-auto text-xs text-gray-400">{{ ($kewanganRecords ?? collect())->count() }} {{ __('common.total') }}</span>
                </h3>
                @if(($kewanganRecords ?? collect())->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50"><tr>
                            <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.bulan') }}</th>
                            <th class="text-right px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.kw_elaun_penuh') }}</th>
                            <th class="text-right px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.kw_elaun_prorate') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.kw_status_bayaran') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.kw_hari_lewat') }}</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($kewanganRecords as $kw)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 text-gray-700 font-medium text-xs">{{ $kw->bulan }}</td>
                                <td class="px-3 py-2 text-right text-xs text-gray-600">RM {{ number_format($kw->elaun_penuh, 2) }}</td>
                                <td class="px-3 py-2 text-right text-xs text-gray-700 font-medium">RM {{ number_format($kw->elaun_prorate, 2) }}</td>
                                <td class="px-3 py-2 text-center">
                                    @php $kwC = match($kw->status_bayaran) { 'Selesai' => 'bg-green-100 text-green-700', 'Lewat' => 'bg-red-100 text-red-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $kwC }}">{{ $kw->status_bayaran }}</span>
                                </td>
                                <td class="px-3 py-2 text-center text-xs {{ $kw->hari_lewat > 7 ? 'text-red-600 font-bold' : 'text-gray-500' }}">{{ $kw->hari_lewat }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-gray-400 text-sm py-4">{{ __('protege.no_records') }}</p>
                @endif
            </div>

            {{-- Training Participation --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    {{ __('protege.trn_title') }}
                    <span class="ml-auto text-xs text-gray-400">{{ ($trainingRecords ?? collect())->count() }} {{ __('common.total') }}</span>
                </h3>
                @if(($trainingRecords ?? collect())->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50"><tr>
                            <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.trn_tajuk') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.trn_sesi') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.status') }}</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Pre</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Post</th>
                            <th class="text-center px-3 py-2 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.trn_improvement') }}</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($trainingRecords as $tp)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 text-gray-700 text-xs font-medium">{{ $tp->trainingRecord?->tajuk_training ?? '-' }}</td>
                                <td class="px-3 py-2 text-center text-xs text-gray-600">{{ $tp->trainingRecord?->sesi ?? '-' }}</td>
                                <td class="px-3 py-2 text-center">
                                    @php $tpC = match($tp->status_kehadiran) { 'Hadir' => 'bg-green-100 text-green-700', 'Lewat' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' }; @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $tpC }}">{{ $tp->status_kehadiran }}</span>
                                </td>
                                <td class="px-3 py-2 text-center text-xs text-gray-600">{{ $tp->pre_assessment_score }}</td>
                                <td class="px-3 py-2 text-center text-xs text-gray-700 font-medium">{{ $tp->post_assessment_score }}</td>
                                <td class="px-3 py-2 text-center text-xs {{ $tp->improvement_pct > 0 ? 'text-green-600' : 'text-gray-500' }}">{{ number_format($tp->improvement_pct, 1) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-gray-400 text-sm py-4">{{ __('protege.no_records') }}</p>
                @endif
            </div>

        </div>
    </div>

    <!-- Background & Registration Tab -->
    <div x-show="tab === 'background'" style="display:none">
        <div class="space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-5">{{ __('common.section_background') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.category') }} ({{ __('common.tab_background') }})</label>
                        <p class="text-gray-800 mt-1">
                            @if($talent->background_type)
                                @php
                                    $bgLabel = match($talent->background_type) {
                                        'anak_atm' => __('common.bg_atm_active'),
                                        'anak_veteran_atm' => __('common.bg_atm_veteran'),
                                        'anak_awam_mindef' => __('common.bg_mindef_civil'),
                                        default => $talent->background_type,
                                    };
                                @endphp
                                {{ $bgLabel }}
                            @else - @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.guardian_name') }}</label>
                        <p class="text-gray-800 mt-1">{{ $talent->guardian_name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.guardian_ic') }}</label>
                        <p class="text-gray-800 mt-1">{{ $talent->guardian_ic ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.guardian_military_no') }}</label>
                        <p class="text-gray-800 mt-1">{{ $talent->guardian_military_no ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.guardian_relationship') }}</label>
                        <p class="text-gray-800 mt-1">{{ $talent->guardian_relationship ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.highest_qualification') }}</label>
                        <p class="text-gray-800 mt-1">{{ $talent->highest_qualification ? __('common.qual_' . $talent->highest_qualification) : '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-5">{{ __('common.application_preferences') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('portal.preferred_sectors') }}</label>
                        <div class="mt-1">
                            @if($talent->preferred_sectors && count($talent->preferred_sectors))
                                <div class="flex flex-wrap gap-2">
                                    @foreach($talent->preferred_sectors as $sector)
                                        <span class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">{{ $sector }}</span>
                                    @endforeach
                                </div>
                            @else <p class="text-gray-800">-</p> @endif
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('portal.preferred_locations') }}</label>
                        <div class="mt-1">
                            @if($talent->preferred_locations && count($talent->preferred_locations))
                                <div class="flex flex-wrap gap-2">
                                    @foreach($talent->preferred_locations as $loc)
                                        <span class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">{{ $loc }}</span>
                                    @endforeach
                                </div>
                            @else <p class="text-gray-800">-</p> @endif
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.employed') }}</label>
                        <p class="text-gray-800 mt-1">{{ $talent->currently_employed ? __('messages.yes') : __('messages.no') }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.available_start_date') }}</label>
                        <p class="text-gray-800 mt-1">{{ $talent->available_start_date?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.pdpa_declaration') }}</label>
                        <p class="text-gray-800 mt-1">
                            @if($talent->pdpa_consent)
                                <span class="inline-flex items-center gap-1 text-green-700"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> {{ __('messages.yes') }}</span>
                            @else
                                <span class="text-gray-500">{{ __('messages.no') }}</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('common.declaration_signature') ?? 'Declaration' }}</label>
                        <p class="text-gray-800 mt-1">{{ $talent->declaration_signature ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Placement Tab -->
    <div x-show="tab === 'placement'" style="display:none">
        <div class="space-y-4">
            @forelse($talent->placements as $placement)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $placement->company?->company_name }}</h4>
                            <p class="text-sm text-gray-500 mt-1">{{ $placement->department ?? __('messages.no_department') }}</p>
                            <div class="flex gap-4 mt-3 text-sm text-gray-600">
                                <span>{{ $placement->start_date?->format('d/m/Y') }} - {{ $placement->end_date?->format('d/m/Y') }}</span>
                                <span>RM {{ number_format($placement->monthly_stipend, 2) }}/{{ __('common.month') }}</span>
                            </div>
                        </div>
                        @include('partials.talent-status-badge', ['status' => $placement->placement_status])
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400">
                    <p>{{ __('messages.no_placement_records') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Feedback Tab -->
    <div x-show="tab === 'feedback'" style="display:none">
        <div class="space-y-4">
            @forelse($feedbackData as $fb)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <span class="text-sm font-semibold text-gray-800">{{ ucfirst($fb->feedback_from) }}</span>
                            <span class="text-xs text-gray-400 ml-2">{{ $fb->submitted_at?->format('d/m/Y') }}</span>
                        </div>
                        <div class="text-lg font-bold text-[#1E3A5F]">{{ $fb->average_score ?? '-' }}/5</div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-3">
                        @foreach([
                            [__('common.scores.technical'), $fb->score_technical],
                            [__('common.scores.communication'), $fb->score_communication],
                            [__('common.scores.discipline'), $fb->score_discipline],
                            [__('common.scores.problem_solving'), $fb->score_problem_solving],
                            [__('common.scores.professionalism'), $fb->score_professionalism],
                        ] as [$label, $score])
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-xs text-gray-500 mb-1">{{ $label }}</div>
                                <div class="text-xl font-bold {{ $score >= 4 ? 'text-green-600' : ($score >= 3 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $score ?? '-' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($fb->comments)
                        <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $fb->comments }}</p>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400">
                    <p>{{ __('messages.no_feedback_yet') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Finance Tab -->
    <div x-show="tab === 'finance'" style="display:none">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">{{ __('common.transaction_records') }}</h3>
                <span class="text-sm font-semibold text-[#1E3A5F]">
                    {{ __('common.total') }}: RM {{ number_format($transactions->sum('amount'), 2) }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.date') }}</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.category') }}</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.description') }}</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.amount_rm') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($transactions as $txn)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ $txn->transaction_date?->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs">{{ ucfirst($txn->category) }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $txn->description ?? '-' }}</td>
                                <td class="px-4 py-3 text-right font-mono font-medium text-gray-800">{{ number_format($txn->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-400">{{ __('messages.no_transaction_records') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Documents Tab -->
    <div x-show="tab === 'documents'" style="display:none">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Documents -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">{{ __('common.tab_documents') }}</h3>
                @forelse($talent->documents as $doc)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-red-100 rounded flex items-center justify-center">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $doc->file_name }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst($doc->document_type) }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                               class="p-1 text-blue-600 hover:bg-blue-50 rounded transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('admin.talents.delete-document', $doc) }}" onsubmit="return confirm('{{ __('messages.confirm_delete_document') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-red-600 hover:bg-red-50 rounded transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm text-center py-4">{{ __('messages.no_documents_uploaded') }}</p>
                @endforelse
            </div>

            <!-- Certifications -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">{{ __('portal.certifications') }}</h3>
                @forelse($talent->certifications as $cert)
                    <div class="p-3 bg-gray-50 rounded-lg mb-2">
                        <div class="font-medium text-gray-800 text-sm">{{ $cert->certification_name }}</div>
                        @if($cert->issuer)
                            <div class="text-xs text-gray-500">{{ $cert->issuer }}</div>
                        @endif
                        @if($cert->issue_date)
                            <div class="text-xs text-gray-400 mt-1">
                                {{ $cert->issue_date?->format('M Y') }}
                                @if($cert->expiry_date) - {{ $cert->expiry_date?->format('M Y') }} @endif
                                @if($cert->isExpired()) <span class="text-red-500 font-medium">({{ __('messages.expired') }})</span> @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-400 text-sm text-center py-4">{{ __('messages.no_certs_registered') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Notes Tab -->
    <div x-show="tab === 'notes'" style="display:none">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('common.admin_notes_internal') }}</h3>
            @if($talent->notes)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap">{{ $talent->notes }}</p>
                </div>
            @else
                <p class="text-gray-400 text-sm">{{ __('messages.no_admin_notes') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
