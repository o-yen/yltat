@extends('layouts.admin')
@section('title', __('nav.manage_placement') . ' — ' . $talent->full_name)
@section('page-title', __('nav.manage_placement'))

@section('content')
<div class="mb-5">
    <a href="{{ route('admin.manage-placement.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ __('protege.back_to_list') }}
    </a>
</div>

{{-- Graduate Header --}}
<div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] rounded-2xl p-6 text-white mb-6">
    <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold flex-shrink-0">
            {{ substr($talent->full_name, 0, 1) }}
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-xl font-bold">{{ $talent->full_name }}</h2>
            <p class="text-blue-200 text-sm">{{ $talent->talent_code ?? $talent->id_graduan }}</p>
            <div class="flex flex-wrap gap-3 mt-2 text-xs text-blue-200">
                @if($talent->syarikatPelaksana)
                    <span>{{ __('common.implementing_company') }}: <strong class="text-white">{{ $talent->syarikatPelaksana->nama_syarikat }}</strong></span>
                @endif
                @if($talent->syarikatPenempatan)
                    <span>{{ __('common.placement_company') }}: <strong class="text-white">{{ $talent->syarikatPenempatan->nama_syarikat }}</strong></span>
                @endif
                @if($talent->status_aktif)
                    @php
                        $sc = ['Aktif' => 'bg-green-400/20 text-green-200 border-green-400/30', 'Tamat' => 'bg-gray-400/20 text-gray-200 border-gray-400/30'];
                    @endphp
                    <span class="border px-2 py-0.5 rounded-full {{ $sc[$talent->status_aktif] ?? 'bg-blue-400/20 text-blue-200 border-blue-400/30' }}">{{ $talent->status_aktif }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div x-data="{ tab: '{{ request('tab', 'programme') }}' }">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 overflow-x-auto">
        <div class="flex min-w-max border-b border-gray-100">
            @foreach([
                ['programme', __('common.tab_protege')],
                ['logbook', __('protege.log_title')],
                ['daily_logs', __('talent.daily_logs_title')],
                ['transactions', __('common.transaction_list')],
                ['documents', __('common.tab_documents_certs')],
                ['training', __('protege.trn_title')],
                ['feedback', __('nav.feedback')],
            ] as [$key, $label])
                <button @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'border-[#1E3A5F] text-[#1E3A5F] font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-5 py-3 text-sm border-b-2 whitespace-nowrap transition-colors">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ═══ Programme / Placement Info ═══ --}}
    <div x-show="tab === 'programme'" style="display:none" x-data="{ showAssignModal: false, showCompleteModal: false, showTerminateModal: false }">

        @if(!$isAssigned)
            {{-- Not assigned — masked card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center relative">
                <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ __('common.no_placement_assigned') }}</h3>
                <p class="text-sm text-gray-500 mb-6">{{ __('common.no_placement_assigned_hint') }}</p>

                @if($canWrite)
                <button @click="showAssignModal = true"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-[#1E3A5F] text-white rounded-xl text-sm font-semibold hover:bg-[#152c47] transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    {{ __('common.assign_placement') }}
                </button>
                @endif
            </div>
        @else
            {{-- Assigned — programme details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h3 class="font-semibold text-gray-800">{{ __('common.tab_protege') }}</h3>
                    @if($canWrite)
                    <div class="flex items-center gap-2">
                        <button @click="showAssignModal = true"
                                class="inline-flex items-center gap-1.5 text-xs text-[#1E3A5F] hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors border border-gray-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            {{ __('common.edit') }}
                        </button>
                        @if($talent->status_aktif === 'Aktif')
                        <button @click="showCompleteModal = true"
                                class="inline-flex items-center gap-1.5 text-xs text-green-700 hover:bg-green-50 px-3 py-1.5 rounded-lg transition-colors border border-green-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('common.complete_placement') }}
                        </button>
                        <button @click="showTerminateModal = true"
                                class="inline-flex items-center gap-1.5 text-xs text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors border border-red-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            {{ __('common.early_termination') }}
                        </button>
                        @endif
                    </div>
                    @endif
                </div>
                {{-- Absorption status for completed placements --}}
                @if($talent->status_aktif === 'Tamat' && $talent->status_penyerapan_6bulan)
                <div class="mb-4 p-3 rounded-lg {{ $talent->status_penyerapan_6bulan === 'Diserap' ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                    <p class="text-sm"><strong>{{ __('common.absorption_status') }}:</strong>
                        <span class="font-semibold {{ $talent->status_penyerapan_6bulan === 'Diserap' ? 'text-green-700' : 'text-gray-700' }}">{{ $talent->status_penyerapan_6bulan }}</span>
                    </p>
                </div>
                @endif
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-xs text-gray-400">{{ __('common.placement_company') }}</dt><dd class="text-gray-800 font-medium mt-0.5">{{ $talent->syarikatPenempatan?->nama_syarikat ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">{{ __('common.implementing_company') }}</dt><dd class="text-gray-800 font-medium mt-0.5">{{ $talent->syarikatPelaksana?->nama_syarikat ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">{{ __('common.job_title') }}</dt><dd class="text-gray-800 mt-0.5">{{ $talent->jawatan ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">{{ __('common.status_label') }}</dt><dd class="text-gray-800 mt-0.5">{{ $talent->status_aktif ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">{{ __('common.start_date') }}</dt><dd class="text-gray-800 mt-0.5">{{ $talent->tarikh_mula?->format('d/m/Y') ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">{{ __('common.end_date') }}</dt><dd class="text-gray-800 mt-0.5">{{ $talent->tarikh_tamat?->format('d/m/Y') ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">{{ __('talent.department_unit') }}</dt><dd class="text-gray-800 mt-0.5">{{ $talent->department ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">{{ __('talent.programme_type') }}</dt><dd class="text-gray-800 mt-0.5">{{ $talent->programme_type ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">{{ __('talent.supervisor_section') }}</dt><dd class="text-gray-800 mt-0.5">{{ $talent->supervisor_name ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">{{ __('talent.supervisor_email') }}</dt><dd class="text-gray-800 mt-0.5">{{ $talent->supervisor_email ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">{{ __('common.monthly_stipend') }}</dt><dd class="text-gray-800 mt-0.5">{{ $talent->monthly_stipend ? 'RM ' . number_format($talent->monthly_stipend, 2) : '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">{{ __('common.duration_months_label') }}</dt><dd class="text-gray-800 mt-0.5">{{ $talent->duration_months ? $talent->duration_months . ' ' . __('common.months') : '-' }}</dd></div>
                </dl>
            </div>
        @endif

        {{-- Assign / Edit Placement Modal --}}
        <div x-show="showAssignModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div class="fixed inset-0 bg-black/50" @click="showAssignModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.away="showAssignModal = false">
                <div class="sticky top-0 bg-gradient-to-r from-[#1E3A5F] to-[#274670] px-6 py-4 rounded-t-2xl flex items-center justify-between">
                    <h3 class="text-white font-semibold">{{ $isAssigned ? __('common.edit_placement') : __('common.assign_placement') }}</h3>
                    <button @click="showAssignModal = false" class="text-white/60 hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form method="POST" action="{{ route('admin.manage-placement.assign', $talent) }}" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.placement_company') }} *</label>
                            <select name="id_syarikat_penempatan" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                <option value="">-- {{ __('messages.select') }} --</option>
                                @foreach($penempatan as $s)
                                    <option value="{{ $s->id_syarikat }}" {{ $talent->id_syarikat_penempatan == $s->id_syarikat ? 'selected' : '' }}>{{ $s->nama_syarikat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.implementing_company') }}</label>
                            <select name="id_pelaksana" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                <option value="">-- {{ __('messages.select') }} --</option>
                                @foreach($pelaksana as $p)
                                    <option value="{{ $p->id_pelaksana }}" {{ $talent->id_pelaksana == $p->id_pelaksana ? 'selected' : '' }}>{{ $p->nama_syarikat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.job_title') }}</label>
                            <input type="text" name="jawatan" value="{{ is_string($talent->jawatan) ? $talent->jawatan : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('talent.department_unit') }}</label>
                            <input type="text" name="department" value="{{ is_string($talent->department) ? $talent->department : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.start_date') }}</label>
                            <input type="date" name="tarikh_mula" value="{{ $talent->tarikh_mula instanceof \Carbon\Carbon ? $talent->tarikh_mula->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.end_date') }}</label>
                            <input type="date" name="tarikh_tamat" value="{{ $talent->tarikh_tamat instanceof \Carbon\Carbon ? $talent->tarikh_tamat->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('talent.supervisor_section') }}</label>
                            <input type="text" name="supervisor_name" value="{{ $talent->supervisor_name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('talent.supervisor_email') }}</label>
                            <input type="email" name="supervisor_email" value="{{ $talent->supervisor_email }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.monthly_stipend') }} (RM)</label>
                            <input type="number" name="monthly_stipend" value="{{ $talent->monthly_stipend }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.absorption_status') }}</label>
                            <select name="status_penyerapan_6bulan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                <option value="">-- {{ __('messages.select') }} --</option>
                                @foreach(['Diserap' => __('common.absorbed'), 'Tidak Diserap' => __('common.not_absorbed'), 'Dalam Proses' => __('common.in_process'), 'Belum Layak' => __('common.not_eligible')] as $val => $label)
                                    <option value="{{ $val }}" {{ ($talent->status_penyerapan_6bulan ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit" class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors">
                            {{ $isAssigned ? __('common.update') : __('common.assign_placement') }}
                        </button>
                        <button type="button" @click="showAssignModal = false" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">{{ __('common.cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Complete Placement Modal --}}
        <div x-show="showCompleteModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div class="fixed inset-0 bg-black/50" @click="showCompleteModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg" @click.away="showCompleteModal = false">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 rounded-t-2xl flex items-center justify-between">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('common.complete_placement') }}
                    </h3>
                    <button @click="showCompleteModal = false" class="text-white/60 hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form method="POST" action="{{ route('admin.manage-placement.complete', $talent) }}" class="p-6">
                    @csrf
                    <p class="text-sm text-gray-600 mb-5">{{ __('common.complete_placement_hint') }}</p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.completion_date') }} *</label>
                            <input type="date" name="completion_date" value="{{ $talent->tarikh_tamat instanceof \Carbon\Carbon ? $talent->tarikh_tamat->format('Y-m-d') : date('Y-m-d') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.absorption_status') }} *</label>
                            <select name="status_penyerapan_6bulan" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">-- {{ __('messages.select') }} --</option>
                                @foreach(['Diserap' => __('common.absorbed'), 'Tidak Diserap' => __('common.not_absorbed'), 'Dalam Proses' => __('common.in_process'), 'Belum Layak' => __('common.not_eligible')] as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.remarks') }}</label>
                            <textarea name="completion_remarks" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="{{ __('common.optional') }}"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 transition-colors">{{ __('common.complete_placement') }}</button>
                        <button type="button" @click="showCompleteModal = false" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">{{ __('common.cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Early Termination Modal --}}
        <div x-show="showTerminateModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div class="fixed inset-0 bg-black/50" @click="showTerminateModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg" @click.away="showTerminateModal = false">
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 rounded-t-2xl flex items-center justify-between">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        {{ __('common.early_termination') }}
                    </h3>
                    <button @click="showTerminateModal = false" class="text-white/60 hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form method="POST" action="{{ route('admin.manage-placement.terminate', $talent) }}" class="p-6">
                    @csrf
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-5">
                        <p class="text-sm text-red-700">{{ __('common.early_termination_warning') }}</p>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.termination_date') }} *</label>
                            <input type="date" name="termination_date" value="{{ date('Y-m-d') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.termination_reason') }} *</label>
                            <textarea name="termination_reason" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit" class="px-6 py-2.5 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors">{{ __('common.confirm_termination') }}</button>
                        <button type="button" @click="showTerminateModal = false" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">{{ __('common.cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══ Logbook ═══ --}}
    <div x-show="tab === 'logbook'" style="display:none">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">{{ __('protege.log_title') }}</h3></div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.bulan') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.log_status_logbook') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.log_status_semakan') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.log_nama_mentor') }}</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logbooks as $lb)
                        <tr class="hover:bg-gray-50"><td class="px-4 py-3">{{ $lb->bulan }} {{ $lb->tahun }}</td><td class="px-4 py-3"><span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $lb->status_logbook === 'Dikemukakan' ? 'bg-green-100 text-green-700' : ($lb->status_logbook === 'Lewat' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">{{ $lb->status_logbook }}</span></td><td class="px-4 py-3"><span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $lb->status_semakan === 'Lulus' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $lb->status_semakan }}</span></td><td class="px-4 py-3 text-gray-600">{{ $lb->nama_mentor ?? '-' }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">{{ __('protege.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══ Daily Logs ═══ --}}
    <div x-show="tab === 'daily_logs'" style="display:none">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">{{ __('talent.daily_logs_title') }}</h3></div>
            @forelse($dailyLogs as $log)
                <div class="px-5 py-4 border-b border-gray-50">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-800">{{ $log->log_date?->format('d M Y') }}</span>
                        @if($log->mood)<span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">{{ $log->mood }}</span>@endif
                    </div>
                    @if($log->activities)<p class="text-sm text-gray-600 mb-1"><strong class="text-gray-500">{{ __('talent.activities_done') }}:</strong> {{ Str::limit($log->activities, 200) }}</p>@endif
                    @if($log->learnings)<p class="text-sm text-gray-600 mb-1"><strong class="text-gray-500">{{ __('talent.today_learnings') }}:</strong> {{ Str::limit($log->learnings, 200) }}</p>@endif
                </div>
            @empty
                <div class="px-5 py-8 text-center text-gray-400">{{ __('protege.no_records') }}</div>
            @endforelse
        </div>
    </div>

    {{-- ═══ Transactions ═══ --}}
    <div x-show="tab === 'transactions'" style="display:none">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">{{ __('common.transaction_list') }}</h3></div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.bulan') }}</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.kw_elaun_penuh') }}</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.kw_elaun_prorate') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.kw_status_bayaran') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.kw_tarikh_bayar') }}</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $tx->bulan }} {{ $tx->tahun }}</td>
                            <td class="px-4 py-3 text-right font-mono">{{ number_format($tx->elaun_penuh ?? 0, 2) }}</td>
                            <td class="px-4 py-3 text-right font-mono font-semibold">{{ number_format($tx->elaun_prorate ?? 0, 2) }}</td>
                            <td class="px-4 py-3 text-center"><span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $tx->status_bayaran === 'Selesai' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ $tx->status_bayaran }}</span></td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $tx->tarikh_bayar ? \Carbon\Carbon::parse($tx->tarikh_bayar)->format('d/m/Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">{{ __('protege.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══ Documents & Certificates (incl. Surat Kuning/Biru) ═══ --}}
    <div x-show="tab === 'documents'" style="display:none">
        {{-- Uploaded documents --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('common.tab_documents_certs') }}</h3>
            @if($talent->documents->isNotEmpty())
                <div class="space-y-2">
                    @foreach($talent->documents as $doc)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $doc->file_name ?? $doc->document_type }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}</p>
                            </div>
                            @if($doc->file_path)
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline">{{ __('protege.view') }}</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm">{{ __('messages.no_documents_uploaded') }}</p>
            @endif
        </div>

        {{-- Surat Kuning / Biru --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.ss_title') }}</h3>
            @if($suratRecords->isNotEmpty())
                <div class="space-y-3">
                    @foreach($suratRecords as $surat)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $surat->jenis_surat === 'Surat Kuning' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">{{ $surat->jenis_surat }}</span>
                                <span class="text-sm text-gray-600 ml-2">{{ $surat->status_surat }}</span>
                                <span class="text-xs text-gray-400 ml-2">PIC: {{ $surat->pic_responsible ?? '-' }}</span>
                            </div>
                            <a href="{{ route('admin.status-surat.show', $surat) }}" class="text-xs text-blue-600 hover:underline">{{ __('protege.view') }}</a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm">{{ __('protege.no_records') }}</p>
            @endif
        </div>
    </div>

    {{-- ═══ Training ═══ --}}
    <div x-show="tab === 'training'" style="display:none">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">{{ __('protege.trn_title') }}</h3></div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.trn_tajuk') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.tarikh') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.trn_kehadiran_pct') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.status') }}</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($trainings as $tp)
                        @php $tr = $tp->trainingRecord; @endphp
                        @if($tr)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3"><span class="font-medium text-gray-800">{{ $tr->tajuk_training }}</span><br><span class="text-xs text-gray-400">{{ $tr->nama_syarikat }}</span></td>
                            <td class="px-4 py-3 text-gray-600">{{ $tr->tarikh_training ? \Carbon\Carbon::parse($tr->tarikh_training)->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 text-center"><span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $tp->status_kehadiran === 'Hadir' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $tp->status_kehadiran ?? '-' }}</span></td>
                            <td class="px-4 py-3 text-center"><span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $tr->status === 'Selesai' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">{{ $tr->status }}</span></td>
                        </tr>
                        @endif
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">{{ __('protege.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══ Review & Feedback ═══ --}}
    <div x-show="tab === 'feedback'" style="display:none">
        {{-- Existing feedback --}}
        <div class="space-y-4 mb-6">
            @forelse($feedbacks as $fb)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            @php $fColors = ['company' => 'bg-blue-100 text-blue-700', 'talent' => 'bg-green-100 text-green-700', 'yltat' => 'bg-purple-100 text-purple-700']; @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $fColors[$fb->feedback_from] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($fb->feedback_from) }}</span>
                            <span class="text-xs text-gray-400 ml-2">{{ $fb->submitted_at?->format('d/m/Y') }}</span>
                        </div>
                        <div class="text-lg font-bold text-[#1E3A5F]">{{ $fb->average_score ? number_format($fb->average_score, 1) : '-' }}/5</div>
                    </div>
                    <div class="grid grid-cols-5 gap-2 mb-3">
                        @foreach(['score_technical' => 'Technical', 'score_communication' => 'Communication', 'score_discipline' => 'Discipline', 'score_problem_solving' => 'Problem Solving', 'score_professionalism' => 'Professionalism'] as $field => $label)
                            <div class="text-center p-2 bg-gray-50 rounded-lg">
                                <p class="text-lg font-bold text-gray-800">{{ $fb->$field ?? '-' }}</p>
                                <p class="text-[10px] text-gray-400">{{ $label }}</p>
                            </div>
                        @endforeach
                    </div>
                    @if($fb->comments)<p class="text-sm text-gray-600 italic">"{{ $fb->comments }}"</p>@endif
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400">{{ __('messages.no_feedback_yet') }}</div>
            @endforelse
        </div>

        {{-- Add Feedback Form --}}
        @if($canWrite && $talent->placements->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('common.add_feedback') }}</h3>
            <form method="POST" action="{{ route('admin.manage-placement.feedback', $talent) }}">
                @csrf
                <input type="hidden" name="placement_id" value="{{ $talent->placements->first()->id }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.source') }}</label>
                        <select name="feedback_from" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="company">{{ __('common.feedback_from.company') }}</option>
                            <option value="yltat">{{ __('common.feedback_from.yltat') }}</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-5 gap-3 mb-4">
                    @foreach(['score_technical' => 'Technical', 'score_communication' => 'Communication', 'score_discipline' => 'Discipline', 'score_problem_solving' => 'Problem Solving', 'score_professionalism' => 'Professionalism'] as $field => $label)
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ $label }}</label>
                            <select name="{{ $field }}" required class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm text-center">
                                @for($i = 1; $i <= 5; $i++) <option value="{{ $i }}">{{ $i }}</option> @endfor
                            </select>
                        </div>
                    @endforeach
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('protege.catatan') }}</label>
                    <textarea name="comments" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                </div>
                <button type="submit" class="px-5 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">{{ __('common.submit') }}</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
