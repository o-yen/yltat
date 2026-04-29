@extends('layouts.talent')

@section('title', __('talent.placement_info_title'))
@section('page-title', __('talent.placement_info_title'))

@section('content')
<div class="space-y-6">

    @if($activePlacement)

        {{-- Active placement hero --}}
        <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] rounded-2xl p-6 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('talent.active_placement') }}</p>
                    <h2 class="text-xl font-bold mt-1">{{ $activePlacement->company->company_name ?? '—' }}</h2>
                    <p class="text-blue-300 text-sm mt-0.5">{{ $activePlacement->department ?? '—' }}</p>
                </div>
                <span class="bg-green-400/20 text-green-200 border border-green-400/30 px-3 py-1 rounded-full text-xs font-medium">
                    {{ __('common.status.' . $activePlacement->placement_status) }}
                </span>
            </div>
            <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 border-t border-white/20">
                <div>
                    <p class="text-blue-200 text-xs">{{ __('talent.placement_start_date') }}</p>
                    <p class="text-white font-medium text-sm">{{ $activePlacement->start_date?->format('d M Y') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-blue-200 text-xs">{{ __('talent.placement_end_date') }}</p>
                    <p class="text-white font-medium text-sm">{{ $activePlacement->end_date?->format('d M Y') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-blue-200 text-xs">{{ __('talent.monthly_allowance') }}</p>
                    <p class="text-white font-bold text-sm">RM {{ number_format($activePlacement->monthly_stipend, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Company Info --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ __('talent.company_info') }}
                </h3>
                @php $company = $activePlacement->company; @endphp
                @if($company)
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs text-gray-400">{{ __('talent.company_name') }}</dt>
                            <dd class="text-sm font-medium text-gray-800 mt-0.5">{{ $company->company_name }}</dd>
                        </div>
                        @if($company->industry_sector)
                            <div>
                                <dt class="text-xs text-gray-400">{{ __('talent.industry_sector') }}</dt>
                                <dd class="text-sm text-gray-800 mt-0.5">{{ $company->industry_sector }}</dd>
                            </div>
                        @endif
                        @if($company->address)
                            <div>
                                <dt class="text-xs text-gray-400">{{ __('talent.address') }}</dt>
                                <dd class="text-sm text-gray-800 mt-0.5 whitespace-pre-line">{{ $company->address }}</dd>
                            </div>
                        @endif
                        @if($company->phone)
                            <div>
                                <dt class="text-xs text-gray-400">{{ __('common.phone') }}</dt>
                                <dd class="text-sm text-gray-800 mt-0.5">
                                    <a href="tel:{{ $company->phone }}" class="text-[#1E3A5F] hover:underline">{{ $company->phone }}</a>
                                </dd>
                            </div>
                        @endif
                        @if($company->email)
                            <div>
                                <dt class="text-xs text-gray-400">{{ __('common.email') }}</dt>
                                <dd class="text-sm text-gray-800 mt-0.5">
                                    <a href="mailto:{{ $company->email }}" class="text-[#1E3A5F] hover:underline">{{ $company->email }}</a>
                                </dd>
                            </div>
                        @endif
                        @if($company->website)
                            <div>
                                <dt class="text-xs text-gray-400">{{ __('talent.website') }}</dt>
                                <dd class="text-sm mt-0.5">
                                    <a href="{{ $company->website }}" target="_blank" class="text-[#1E3A5F] hover:underline">{{ $company->website }}</a>
                                </dd>
                            </div>
                        @endif
                    </dl>
                @else
                    <p class="text-gray-400 text-sm">{{ __('talent.company_info_unavailable') }}</p>
                @endif
            </div>

            {{-- Reporting Manager / Supervisor --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ __('talent.supervisor_section') }}
                </h3>

                @if($activePlacement->supervisor_name)
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-14 h-14 rounded-full bg-[#1E3A5F] flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                            {{ substr($activePlacement->supervisor_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $activePlacement->supervisor_name }}</p>
                            <p class="text-sm text-gray-500">{{ __('talent.placement_supervisor') }}</p>
                        </div>
                    </div>
                    <dl class="space-y-3 border-t border-gray-100 pt-4">
                        @if($activePlacement->supervisor_email)
                            <div>
                                <dt class="text-xs text-gray-400">{{ __('talent.supervisor_email') }}</dt>
                                <dd class="text-sm mt-0.5">
                                    <a href="mailto:{{ $activePlacement->supervisor_email }}" class="text-[#1E3A5F] hover:underline">
                                        {{ $activePlacement->supervisor_email }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                        @if($activePlacement->department)
                            <div>
                                <dt class="text-xs text-gray-400">{{ __('talent.department_unit') }}</dt>
                                <dd class="text-sm text-gray-800 mt-0.5">{{ $activePlacement->department }}</dd>
                            </div>
                        @endif
                        @if($activePlacement->programme_type)
                            <div>
                                <dt class="text-xs text-gray-400">{{ __('talent.programme_type') }}</dt>
                                <dd class="text-sm text-gray-800 mt-0.5">{{ $activePlacement->programme_type }}</dd>
                            </div>
                        @endif
                    </dl>
                @else
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <p class="text-gray-400 text-sm">{{ __('talent.supervisor_info_missing') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Company contact person (if exists) --}}
        @if($company?->contact_name || $company?->contact_email)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ __('talent.company_rep_section') }}
                </h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($company?->contact_name)
                        <div>
                            <dt class="text-xs text-gray-400">{{ __('talent.representative_name') }}</dt>
                            <dd class="text-sm font-medium text-gray-800 mt-0.5">{{ $company->contact_name }}</dd>
                        </div>
                    @endif
                    @if($company?->contact_email)
                        <div>
                            <dt class="text-xs text-gray-400">{{ __('talent.representative_email') }}</dt>
                            <dd class="text-sm mt-0.5">
                                <a href="mailto:{{ $company->contact_email }}" class="text-[#1E3A5F] hover:underline">{{ $company->contact_email }}</a>
                            </dd>
                        </div>
                    @endif
                    @if($company?->contact_phone)
                        <div>
                            <dt class="text-xs text-gray-400">{{ __('talent.representative_phone') }}</dt>
                            <dd class="text-sm mt-0.5">
                                <a href="tel:{{ $company->contact_phone }}" class="text-[#1E3A5F] hover:underline">{{ $company->contact_phone }}</a>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endif

    @elseif($hasProtegeData)

        {{-- PROTEGE programme data from talent record --}}
        <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] rounded-2xl p-6 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('talent.active_placement') }}</p>
                    <h2 class="text-xl font-bold mt-1">{{ $talent->syarikatPenempatan?->nama_syarikat ?? $talent->syarikatPelaksana?->nama_syarikat ?? '—' }}</h2>
                    <p class="text-blue-300 text-sm mt-0.5">{{ $talent->department ?? $talent->jawatan ?? '—' }}</p>
                </div>
                @if($talent->status_aktif)
                    @php
                        $protegeStatusColors = [
                            'Aktif' => 'bg-green-400/20 text-green-200 border-green-400/30',
                            'Tamat' => 'bg-gray-400/20 text-gray-200 border-gray-400/30',
                            'Dalam Proses' => 'bg-yellow-400/20 text-yellow-200 border-yellow-400/30',
                        ];
                    @endphp
                    <span class="border px-3 py-1 rounded-full text-xs font-medium {{ $protegeStatusColors[$talent->status_aktif] ?? 'bg-blue-400/20 text-blue-200 border-blue-400/30' }}">
                        {{ $talent->status_aktif }}
                    </span>
                @endif
            </div>
            <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 border-t border-white/20">
                <div>
                    <p class="text-blue-200 text-xs">{{ __('talent.placement_start_date') }}</p>
                    <p class="text-white font-medium text-sm">{{ $talent->tarikh_mula?->format('d M Y') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-blue-200 text-xs">{{ __('talent.placement_end_date') }}</p>
                    <p class="text-white font-medium text-sm">{{ $talent->tarikh_tamat?->format('d M Y') ?? '—' }}</p>
                </div>
                @if($talent->monthly_stipend)
                    <div>
                        <p class="text-blue-200 text-xs">{{ __('talent.monthly_allowance') }}</p>
                        <p class="text-white font-bold text-sm">RM {{ number_format($talent->monthly_stipend, 2) }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Programme details --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ __('talent.programme_details') }}
                </h3>
                <dl class="space-y-3">
                    @if($talent->jawatan)
                        <div>
                            <dt class="text-xs text-gray-400">{{ __('common.job_title') }}</dt>
                            <dd class="text-sm font-medium text-gray-800 mt-0.5">{{ $talent->jawatan }}</dd>
                        </div>
                    @endif
                    @if($talent->syarikatPelaksana)
                        <div>
                            <dt class="text-xs text-gray-400">{{ __('common.implementing_company') }}</dt>
                            <dd class="text-sm text-gray-800 mt-0.5">{{ $talent->syarikatPelaksana->nama_syarikat }}</dd>
                        </div>
                    @endif
                    @if($talent->syarikatPenempatan)
                        <div>
                            <dt class="text-xs text-gray-400">{{ __('common.placement_company') }}</dt>
                            <dd class="text-sm text-gray-800 mt-0.5">{{ $talent->syarikatPenempatan->nama_syarikat }}</dd>
                        </div>
                    @endif
                    @if($talent->department)
                        <div>
                            <dt class="text-xs text-gray-400">{{ __('talent.department_unit') }}</dt>
                            <dd class="text-sm text-gray-800 mt-0.5">{{ $talent->department }}</dd>
                        </div>
                    @endif
                    @if($talent->programme_type)
                        <div>
                            <dt class="text-xs text-gray-400">{{ __('talent.programme_type') }}</dt>
                            <dd class="text-sm text-gray-800 mt-0.5">{{ $talent->programme_type }}</dd>
                        </div>
                    @endif
                    @if($talent->duration_months)
                        <div>
                            <dt class="text-xs text-gray-400">{{ __('talent.duration') }}</dt>
                            <dd class="text-sm text-gray-800 mt-0.5">{{ $talent->duration_months }} {{ __('common.months') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Supervisor --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ __('talent.supervisor_section') }}
                </h3>

                @if($talent->supervisor_name)
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-14 h-14 rounded-full bg-[#1E3A5F] flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                            {{ substr($talent->supervisor_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $talent->supervisor_name }}</p>
                            <p class="text-sm text-gray-500">{{ __('talent.placement_supervisor') }}</p>
                        </div>
                    </div>
                    @if($talent->supervisor_email)
                        <dl class="border-t border-gray-100 pt-4">
                            <div>
                                <dt class="text-xs text-gray-400">{{ __('talent.supervisor_email') }}</dt>
                                <dd class="text-sm mt-0.5">
                                    <a href="mailto:{{ $talent->supervisor_email }}" class="text-[#1E3A5F] hover:underline">{{ $talent->supervisor_email }}</a>
                                </dd>
                            </div>
                        </dl>
                    @endif
                @else
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <p class="text-gray-400 text-sm">{{ __('talent.supervisor_info_missing') }}</p>
                    </div>
                @endif
            </div>
        </div>

    @else
        {{-- No active placement --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <p class="text-gray-600 font-medium">{{ __('talent.no_active_placement') }}</p>
            <p class="text-gray-400 text-sm mt-1">{{ __('talent.placement_info_pending') }}</p>
        </div>
    @endif

    {{-- Placement history --}}
    @if($placementHistory->count() > 1 || ($placementHistory->count() === 1 && ! $activePlacement))
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('talent.placement_history') }}</h3>
            <div class="space-y-3">
                @foreach($placementHistory as $placement)
                    @if($placement->id !== $activePlacement?->id)
                        <div class="flex items-center gap-4 p-3 rounded-lg border border-gray-100">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ $placement->company->company_name ?? '—' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $placement->start_date?->format('M Y') }} — {{ $placement->end_date?->format('M Y') }}
                                    @if($placement->batch) · {{ $placement->batch->batch_name }} @endif
                                </p>
                            </div>
                            @php
                                $hColors = ['completed' => 'bg-green-100 text-green-700', 'terminated' => 'bg-red-100 text-red-700', 'planned' => 'bg-blue-100 text-blue-700'];
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $hColors[$placement->placement_status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ __('common.status.' . $placement->placement_status) }}
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
