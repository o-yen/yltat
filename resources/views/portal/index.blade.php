@extends('layouts.public')

@section('title', __('portal.title'))

@section('content')
<!-- Hero Section -->
<div class="relative py-16 overflow-hidden">
    <div class="relative max-w-4xl mx-auto px-4 text-center">
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('images/protege-mindef-logo.png') }}" alt="Protege MINDEF" class="h-[4.5rem] w-auto max-h-[4.5rem] drop-shadow-[0_12px_28px_rgba(39,70,112,0.18)]">
        </div>
        <h1 class="mb-3 text-3xl font-bold text-slate-900 md:text-4xl">{{ __('portal.title') }}</h1>
        <p class="mb-8 text-lg text-slate-600">{{ __('portal.subtitle') }}</p>

        <!-- Register CTA -->
        <div class="mb-8">
            <a href="{{ route('portal.register') }}"
               class="inline-flex items-center gap-2 bg-[#C8102E] hover:bg-red-700 text-white font-bold px-7 py-3 rounded-xl shadow-lg transition-all hover:shadow-xl hover:-translate-y-0.5 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                {{ __('portal.register_cta') }}
            </a>
            <p class="text-slate-400 text-xs mt-2">{{ __('portal.register_hint') }}</p>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('portal.index') }}" class="max-w-2xl mx-auto">
            <div class="rounded-2xl border border-slate-200/60 bg-white/90 p-4 shadow-[0_20px_50px_rgba(39,70,112,0.08)] backdrop-blur-xl">
                <div class="flex gap-3">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="{{ __('portal.search_placeholder') }}"
                           class="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-[#274670]">
                    <button type="submit" class="rounded-xl bg-[#274670] px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#1f3a5c]">
                        {{ __('portal.search_button') }}
                    </button>
                </div>

                <!-- Advanced Filters -->
                <div class="mt-3 flex flex-wrap gap-3 border-t border-slate-100 pt-3">
                    <select name="university" class="min-w-28 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#274670]">
                        <option value="">{{ __('portal.university_placeholder') }}</option>
                        @foreach($universities as $uni)
                            <option value="{{ $uni }}" {{ request('university') === $uni ? 'selected' : '' }}>{{ $uni }}</option>
                        @endforeach
                    </select>
                    <select name="programme" class="min-w-28 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#274670]">
                        <option value="">{{ __('portal.programme_filter_placeholder') }}</option>
                        @foreach($programmes as $prog)
                            <option value="{{ $prog }}" {{ request('programme') === $prog ? 'selected' : '' }}>{{ $prog }}</option>
                        @endforeach
                    </select>
                    <select name="graduation_year" class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#274670]">
                        <option value="">{{ __('portal.graduation_year') }}</option>
                        @foreach(range(date('Y'), date('Y') - 5) as $yr)
                            <option value="{{ $yr }}" {{ request('graduation_year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Results -->
<div class="max-w-7xl mx-auto px-4 py-10">
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
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-slate-800">
            {{ __('portal.results_found', ['count' => $talents->total()]) }}
            @if(request()->hasAny(['search', 'university', 'programme', 'graduation_year']))
                <span class="text-sm font-normal text-slate-500">{{ __('portal.results_for_search') }}</span>
            @endif
        </h2>
        @if(request()->hasAny(['search', 'university', 'programme', 'graduation_year']))
            <a href="{{ route('portal.index') }}" class="text-sm text-blue-600 hover:text-blue-800">{{ __('portal.clear_search') }}</a>
        @endif
    </div>

    @if($talents->isEmpty())
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-slate-400 text-lg">{{ __('portal.empty_title') }}</p>
            <p class="text-slate-400 text-sm mt-1">{{ __('portal.empty_description') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($talents as $talent)
                @php $requestStatus = $requestStatuses[$talent->id] ?? null; @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg hover:border-slate-200 hover:-translate-y-0.5 transition-all duration-200 overflow-hidden group">
                    <a href="{{ route('portal.show', $talent) }}" class="block">

                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-[#1E3A5F] to-[#2d5a8e] p-5">
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-white text-xl font-bold mb-3">
                            {{ substr($talent->full_name, 0, 1) }}
                        </div>
                        <h3 class="text-white font-bold text-sm leading-tight group-hover:text-blue-100 transition-colors">
                            {{ $talent->full_name }}
                        </h3>
                        @if($talent->programme)
                            <p class="text-blue-200 text-xs mt-1 truncate">{{ $talent->programme }}</p>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="p-4">
                        @if($talent->university)
                            <div class="flex items-center gap-2 text-gray-600 text-xs mb-2">
                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                                <span class="truncate">{{ $talent->university }}</span>
                            </div>
                        @endif

                        @if($talent->graduation_year)
                            <div class="flex items-center gap-2 text-gray-500 text-xs mb-3">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ __('portal.graduate_short', ['year' => $talent->graduation_year]) }}
                            </div>
                        @endif

                        @if($talent->skills_text)
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice(explode(',', $talent->skills_text), 0, 3) as $skill)
                                    @if(trim($skill))
                                        <span class="bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ trim($skill) }}</span>
                                    @endif
                                @endforeach
                                @if(count(explode(',', $talent->skills_text)) > 3)
                                    <span class="bg-gray-50 text-gray-500 text-xs px-2 py-0.5 rounded-full">{{ __('portal.more_skills', ['count' => count(explode(',', $talent->skills_text)) - 3]) }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    </a>
                    @if(auth()->check() && auth()->user()->role?->role_name === 'syarikat_pelaksana')
                        <div class="flex items-center justify-between gap-3 border-t border-slate-100 px-4 py-4">
                            @if($requestStatus)
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $requestStatus === 'approved' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $requestStatus === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $requestStatus === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}">
                                    {{ ucfirst($requestStatus) }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400">{{ __('portal.request_applicant_hint') }}</span>
                            @endif

                            <form method="POST" action="{{ route('portal.request-applicant', $talent) }}">
                                @csrf
                                <button type="submit"
                                        class="rounded-xl bg-[#274670] px-4 py-2 text-xs font-semibold text-white transition-colors hover:bg-[#1f3a5c] disabled:cursor-not-allowed disabled:opacity-60"
                                        {{ $requestStatus === 'pending' || $requestStatus === 'approved' ? 'disabled' : '' }}>
                                    {{ __('portal.request_applicant') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $talents->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
