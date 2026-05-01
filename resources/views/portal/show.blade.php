@extends('layouts.public')

@section('title', $talent->full_name . ' — ' . __('portal.title'))

@section('content')
@php
    $hasRichContent = $talent->profile_summary
        || $talent->skills_text
        || !empty($talent->preferred_sectors)
        || !empty($talent->preferred_locations)
        || $certifications->count() > 0;

    $qualMap = ['diploma'=>'Diploma','ijazah'=>__('common.qual_degree'),'sarjana'=>__('common.qual_masters'),'phd'=>'PhD','lain'=>__('common.qual_other')];

    $kategoriColors = [
        'Anak ATM'         => 'bg-blue-500/30 text-blue-100',
        'Anak Veteran'     => 'bg-emerald-500/30 text-emerald-100',
        'Anak Awam MINDEF' => 'bg-purple-500/30 text-purple-100',
    ];
    $bgTypeToKategori = [
        'anak_atm'         => 'Anak ATM',
        'anak_veteran_atm' => 'Anak Veteran',
        'anak_awam_mindef' => 'Anak Awam MINDEF',
    ];
    $displayKategori = $talent->kategori ?: ($bgTypeToKategori[$talent->background_type] ?? null);
@endphp

<div class="max-w-5xl mx-auto px-4 py-10">
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <a href="{{ route('portal.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#1E3A5F] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            {{ __('portal.back_to_portal') }}
        </a>
    </div>

    {{-- Profile Header --}}
    <div class="bg-gradient-to-r from-[#1E3A5F] to-[#2d5a8e] rounded-2xl p-6 sm:p-8 text-white mb-6 shadow-lg">
        <div class="flex flex-col sm:flex-row items-start gap-5">
            @if($talent->profile_photo)
                <img src="{{ asset("storage/{$talent->profile_photo}") }}" alt="{{ $talent->full_name }}"
                     class="w-20 h-20 rounded-2xl object-cover flex-shrink-0 border-2 border-white/30">
            @else
                <div class="w-20 h-20 rounded-2xl bg-white/20 flex items-center justify-center text-3xl font-bold flex-shrink-0 border-2 border-white/30">
                    {{ substr($talent->full_name, 0, 1) }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold">{{ $talent->full_name }}</h1>
                @if($talent->jawatan)
                    <p class="text-blue-100 font-medium mt-0.5">{{ $talent->jawatan }}</p>
                @endif
                @if($talent->programme)
                    <p class="text-blue-200 mt-1 text-sm">{{ $talent->programme }}</p>
                @endif
                @if($talent->university)
                    <p class="text-blue-300 text-sm">{{ $talent->university }}{{ $talent->graduation_year ? ' · ' . $talent->graduation_year : '' }}</p>
                @endif
                <div class="flex flex-wrap gap-2 mt-3">
                    @if($displayKategori)
                        <span class="text-xs px-3 py-1 rounded-full font-semibold {{ $kategoriColors[$displayKategori] ?? 'bg-white/15 text-white' }}">{{ $displayKategori }}</span>
                    @endif
                    @if($talent->highest_qualification)
                        <span class="bg-white/15 text-white text-xs px-3 py-1 rounded-full font-medium">{{ $qualMap[$talent->highest_qualification] ?? $talent->highest_qualification }}</span>
                    @endif
                    @if($talent->cgpa)
                        <span class="bg-white/15 text-white text-xs px-3 py-1 rounded-full font-medium">CGPA {{ number_format($talent->cgpa, 2) }}</span>
                    @endif
                    @if($talent->negeri)
                        <span class="bg-white/10 text-blue-200 text-xs px-3 py-1 rounded-full font-medium">
                            <svg class="w-3 h-3 inline mr-0.5 -mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                            {{ $talent->negeri }}
                        </span>
                    @endif
                    @if($talent->available_start_date && $talent->available_start_date->isFuture())
                        <span class="bg-emerald-500/30 text-emerald-100 text-xs px-3 py-1 rounded-full font-medium">{{ __('portal.available_from') }} {{ $talent->available_start_date->format('M Y') }}</span>
                    @elseif(!$talent->currently_employed)
                        <span class="bg-emerald-500/30 text-emerald-100 text-xs px-3 py-1 rounded-full font-medium">{{ __('portal.available_now') }}</span>
                    @endif
                </div>
                @if(auth()->check() && auth()->user()->role?->role_name === 'syarikat_pelaksana')
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        @if($requestStatus)
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                {{ $requestStatus === 'approved' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $requestStatus === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $requestStatus === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}">
                                {{ __('portal.request_status') }}: {{ ucfirst($requestStatus) }}
                            </span>
                        @endif
                        <form method="POST" action="{{ route('portal.request-applicant', $talent) }}">
                            @csrf
                            <button type="submit"
                                    class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-[#1E3A5F] transition-colors hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-60"
                                    {{ $requestStatus === 'pending' || $requestStatus === 'approved' ? 'disabled' : '' }}>
                                {{ __('portal.request_applicant') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- LAYOUT: 2-column if rich content, single-column compact otherwise --}}
    @if($hasRichContent)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- LEFT: Rich content --}}
        <div class="lg:col-span-2 space-y-5">
            @if($talent->profile_summary)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ __('portal.about_me') }}
                </h2>
                <p class="text-gray-600 leading-relaxed text-sm whitespace-pre-line">{{ $talent->profile_summary }}</p>
            </div>
            @endif

            @if($talent->skills_text)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    {{ __('portal.skills') }}
                </h2>
                <div class="flex flex-wrap gap-2">
                    @foreach(preg_split('/[,\n]+/', $talent->skills_text) as $skill)
                        @if(trim($skill))
                            <span class="bg-blue-50 text-[#1E3A5F] border border-blue-100 px-3 py-1.5 rounded-full text-sm font-medium">{{ trim($skill) }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            @if(!empty($talent->preferred_sectors))
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ __('portal.preferred_sectors') }}
                </h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($talent->preferred_sectors as $sector)
                        <span class="bg-[#1E3A5F]/8 text-[#1E3A5F] border border-[#1E3A5F]/20 px-3 py-1.5 rounded-full text-sm font-medium">{{ $sector }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if(!empty($talent->preferred_locations))
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('portal.preferred_locations') }}
                </h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($talent->preferred_locations as $loc)
                        <span class="bg-slate-50 text-slate-700 border border-slate-200 px-3 py-1.5 rounded-full text-sm font-medium">
                            <svg class="w-3 h-3 inline mr-1 -mt-0.5 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                            {{ $loc }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($certifications->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    {{ __('portal.certifications') }}
                </h2>
                <div class="space-y-3">
                    @foreach($certifications as $cert)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-8 h-8 bg-[#1E3A5F] rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 text-sm">{{ $cert->certification_name }}</p>
                            @if($cert->issuer)<p class="text-xs text-gray-500">{{ $cert->issuer }}</p>@endif
                            @if($cert->issue_date)<p class="text-xs text-gray-400 mt-0.5">{{ $cert->issue_date?->format('M Y') }}</p>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT: Sidebar --}}
        <div class="space-y-5">
            @include('portal._profile_sidebar')
        </div>
    </div>

    @else
    {{-- COMPACT LAYOUT: No rich content — use 2-column card grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @include('portal._profile_sidebar')
    </div>
    @endif

</div>
@endsection
