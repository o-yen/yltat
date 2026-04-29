@extends('layouts.admin')

@section('title', __('guide.title') . ' — ' . __('nav.dashboard'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Back --}}
    <a href="{{ route('admin.system-guide.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ __('guide.back_to_guides') }}
    </a>

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] rounded-xl p-6 text-white">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-lg bg-white/10 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div>
                <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('guide.module_guide') }}</p>
                <h1 class="text-xl font-bold">{{ __('nav.dashboard') }}</h1>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="p-6 md:p-8 space-y-8 text-sm text-gray-700 leading-relaxed">

            {{-- Overview --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">1</span>
                    {{ __('guide.section_overview') }}
                </h2>
                <p>{{ __('guide.dashboard_overview') }}</p>
            </section>

            {{-- Who can access --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">2</span>
                    {{ __('guide.section_access') }}
                </h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="mb-2 font-medium text-gray-800">{{ __('guide.dashboard_access_intro') }}</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></span>
                            <span><strong>{{ __('guide.dashboard_exec_title') }}</strong> — {{ __('guide.dashboard_exec_desc') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mt-1.5 flex-shrink-0"></span>
                            <span><strong>{{ __('guide.dashboard_pelaksana_title') }}</strong> — {{ __('guide.dashboard_pelaksana_desc') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-purple-500 mt-1.5 flex-shrink-0"></span>
                            <span><strong>{{ __('guide.dashboard_rakan_title') }}</strong> — {{ __('guide.dashboard_rakan_desc') }}</span>
                        </li>
                    </ul>
                </div>
            </section>

            {{-- Key Features --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">3</span>
                    {{ __('guide.section_features') }}
                </h2>

                <h3 class="font-semibold text-gray-800 mt-4 mb-2">{{ __('guide.dashboard_exec_title') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                    @foreach([
                        ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'text' => 'guide.dashboard_feat_graduates'],
                        ['icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'text' => 'guide.dashboard_feat_kpi'],
                        ['icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z', 'text' => 'guide.dashboard_feat_charts'],
                        ['icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'text' => 'guide.dashboard_feat_alerts'],
                    ] as $feat)
                        <div class="flex items-start gap-3 bg-blue-50 rounded-lg p-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feat['icon'] }}"/>
                            </svg>
                            <span>{{ __($feat['text']) }}</span>
                        </div>
                    @endforeach
                </div>

                <h3 class="font-semibold text-gray-800 mt-4 mb-2">{{ __('guide.dashboard_pelaksana_title') }}</h3>
                <ul class="space-y-1.5 ml-4">
                    @foreach(['guide.dashboard_pelaksana_f1', 'guide.dashboard_pelaksana_f2', 'guide.dashboard_pelaksana_f3'] as $f)
                        <li class="flex items-start gap-2"><span class="text-emerald-500 mt-0.5">&#10003;</span> {{ __($f) }}</li>
                    @endforeach
                </ul>

                <h3 class="font-semibold text-gray-800 mt-4 mb-2">{{ __('guide.dashboard_rakan_title') }}</h3>
                <ul class="space-y-1.5 ml-4">
                    @foreach(['guide.dashboard_rakan_f1', 'guide.dashboard_rakan_f2', 'guide.dashboard_rakan_f3'] as $f)
                        <li class="flex items-start gap-2"><span class="text-purple-500 mt-0.5">&#10003;</span> {{ __($f) }}</li>
                    @endforeach
                </ul>
            </section>

            {{-- KPI Cards --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">4</span>
                    {{ __('guide.section_kpi_cards') }}
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('guide.th_card') }}</th>
                                <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('guide.th_shows') }}</th>
                                <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('guide.th_color') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr><td class="px-4 py-2 font-medium">{{ __('guide.kpi_active_graduates') }}</td><td class="px-4 py-2">{{ __('guide.kpi_active_graduates_desc') }}</td><td class="px-4 py-2"><span class="inline-block w-3 h-3 rounded-full bg-blue-500"></span> {{ __('guide.color_blue') }}</td></tr>
                            <tr><td class="px-4 py-2 font-medium">{{ __('guide.kpi_surat_kuning') }}</td><td class="px-4 py-2">{{ __('guide.kpi_surat_kuning_desc') }}</td><td class="px-4 py-2"><span class="inline-block w-3 h-3 rounded-full bg-yellow-500"></span> {{ __('guide.color_yellow') }}</td></tr>
                            <tr><td class="px-4 py-2 font-medium">{{ __('guide.kpi_surat_biru') }}</td><td class="px-4 py-2">{{ __('guide.kpi_surat_biru_desc') }}</td><td class="px-4 py-2"><span class="inline-block w-3 h-3 rounded-full bg-blue-400"></span> {{ __('guide.color_blue') }}</td></tr>
                            <tr><td class="px-4 py-2 font-medium">{{ __('guide.kpi_index') }}</td><td class="px-4 py-2">{{ __('guide.kpi_index_desc') }}</td><td class="px-4 py-2">{{ __('guide.kpi_index_color') }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Tips --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">5</span>
                    {{ __('guide.section_tips') }}
                </h2>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 space-y-2">
                    @foreach(['guide.dashboard_tip1', 'guide.dashboard_tip2', 'guide.dashboard_tip3'] as $tip)
                        <p class="flex items-start gap-2">
                            <span class="text-amber-500 font-bold mt-0.5">!</span>
                            {{ __($tip) }}
                        </p>
                    @endforeach
                </div>
            </section>

        </div>
    </div>

    {{-- Navigation --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.system-guide.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; {{ __('guide.back_to_guides') }}</a>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-[#1E3A5F] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#274670] transition-colors">
            {{ __('guide.go_to_module') }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    </div>
</div>
@endsection
