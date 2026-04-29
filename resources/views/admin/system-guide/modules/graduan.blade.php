@extends('layouts.admin')

@section('title', __('guide.title') . ' — ' . __('nav.graduan'))

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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('guide.module_guide') }}</p>
                <h1 class="text-xl font-bold">{{ __('nav.graduan') }}</h1>
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
                <p>{{ __('guide.graduan_overview') }}</p>
            </section>

            {{-- Who can access --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">2</span>
                    {{ __('guide.section_access') }}
                </h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p>{{ __('guide.graduan_access') }}</p>
                </div>
            </section>

            {{-- What You Can Do --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">3</span>
                    {{ __('guide.section_features') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach([
                        'guide.graduan_feat_browse',
                        'guide.graduan_feat_create',
                        'guide.graduan_feat_edit',
                        'guide.graduan_feat_view',
                        'guide.graduan_feat_docs',
                        'guide.graduan_feat_filter',
                    ] as $feat)
                        <div class="flex items-start gap-2 bg-blue-50 rounded-lg p-3">
                            <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ __($feat) }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Step by Step: Add Graduate --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">4</span>
                    {{ __('guide.graduan_howto_add_title') }}
                </h2>
                <ol class="space-y-3 ml-1">
                    @foreach(['guide.graduan_step1', 'guide.graduan_step2', 'guide.graduan_step3', 'guide.graduan_step4', 'guide.graduan_step5'] as $i => $step)
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#1E3A5F] text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">{{ $i + 1 }}</span>
                            <span>{{ __($step) }}</span>
                        </li>
                    @endforeach
                </ol>
            </section>

            {{-- Tabs Reference --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">5</span>
                    {{ __('guide.graduan_tabs_title') }}
                </h2>
                <div class="space-y-3">
                    @foreach([
                        ['name' => 'guide.graduan_tab_personal', 'desc' => 'guide.graduan_tab_personal_desc'],
                        ['name' => 'guide.graduan_tab_protege', 'desc' => 'guide.graduan_tab_protege_desc'],
                        ['name' => 'guide.graduan_tab_background', 'desc' => 'guide.graduan_tab_background_desc'],
                        ['name' => 'guide.graduan_tab_placements', 'desc' => 'guide.graduan_tab_placements_desc'],
                        ['name' => 'guide.graduan_tab_feedback', 'desc' => 'guide.graduan_tab_feedback_desc'],
                        ['name' => 'guide.graduan_tab_finance', 'desc' => 'guide.graduan_tab_finance_desc'],
                        ['name' => 'guide.graduan_tab_docs', 'desc' => 'guide.graduan_tab_docs_desc'],
                        ['name' => 'guide.graduan_tab_notes', 'desc' => 'guide.graduan_tab_notes_desc'],
                    ] as $tab)
                        <div class="flex items-start gap-3 border border-gray-100 rounded-lg p-3">
                            <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-semibold flex-shrink-0 mt-0.5">{{ __($tab['name']) }}</span>
                            <span>{{ __($tab['desc']) }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Status Reference --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">6</span>
                    {{ __('guide.graduan_statuses_title') }}
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('guide.th_status') }}</th>
                                <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('guide.th_meaning') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach([
                                ['Aktif', 'guide.status_aktif_desc'],
                                ['Tamat', 'guide.status_tamat_desc'],
                                ['Berhenti Awal', 'guide.status_berhenti_desc'],
                                ['Applied', 'guide.status_applied_desc'],
                                ['Shortlisted', 'guide.status_shortlisted_desc'],
                                ['Approved', 'guide.status_approved_desc'],
                            ] as [$status, $descKey])
                                <tr>
                                    <td class="px-4 py-2 font-medium">{{ $status }}</td>
                                    <td class="px-4 py-2">{{ __($descKey) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Filter Reference --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">7</span>
                    {{ __('guide.graduan_filters_title') }}
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('guide.th_filter') }}</th>
                                <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('guide.th_description') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach([
                                ['guide.filter_search', 'guide.filter_search_desc'],
                                ['guide.filter_status', 'guide.filter_status_desc'],
                                ['guide.filter_university', 'guide.filter_university_desc'],
                                ['guide.filter_pelaksana', 'guide.filter_pelaksana_desc'],
                                ['guide.filter_penempatan', 'guide.filter_penempatan_desc'],
                                ['guide.filter_kategori', 'guide.filter_kategori_desc'],
                            ] as [$name, $desc])
                                <tr>
                                    <td class="px-4 py-2 font-medium">{{ __($name) }}</td>
                                    <td class="px-4 py-2">{{ __($desc) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Tips --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">8</span>
                    {{ __('guide.section_tips') }}
                </h2>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 space-y-2">
                    @foreach(['guide.graduan_tip1', 'guide.graduan_tip2', 'guide.graduan_tip3', 'guide.graduan_tip4'] as $tip)
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
        <a href="{{ route('admin.talents.index') }}" class="inline-flex items-center gap-2 bg-[#1E3A5F] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#274670] transition-colors">
            {{ __('guide.go_to_module') }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    </div>
</div>
@endsection
