@extends('layouts.public')

@section('title', __('portal.register_page_title') . ' — Protege MINDEF')

@push('styles')
<style>
    .step-panel { display: none; }
    .step-panel.active { display: block; }
    .step-indicator.done { background-color: #274670; color: #fff; }
    .step-indicator.current { background-color: #C8102E; color: #fff; box-shadow: 0 0 0 4px rgba(200,16,46,0.18); }
    .step-indicator.pending { background-color: #e5e7eb; color: #6b7280; }
    .step-line.done { background-color: #274670; }
    .step-line.pending { background-color: #e5e7eb; }
    .form-label { display: block; font-size: 0.8125rem; font-weight: 600; color: #374151; margin-bottom: 0.35rem; }
    .form-input { width: 100%; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.6rem 0.875rem; font-size: 0.875rem; color: #111827; background: #fff; transition: border-color 0.15s, box-shadow 0.15s; outline: none; }
    .form-input:focus { border-color: #274670; box-shadow: 0 0 0 3px rgba(39,70,112,0.12); }
    .form-input.error { border-color: #ef4444; }
    .radio-card { cursor: pointer; }
    .radio-card input[type=radio]:checked + .radio-inner { border-color: #274670; background: rgba(39,70,112,0.06); }
    .check-card input[type=checkbox]:checked + .check-inner { border-color: #274670; background: rgba(39,70,112,0.06); }
    .file-drop { border: 2px dashed #d1d5db; border-radius: 0.75rem; padding: 1.5rem; text-align: center; background: #f9fafb; cursor: pointer; transition: border-color 0.15s; }
    .file-drop:hover, .file-drop.has-file { border-color: #274670; background: rgba(39,70,112,0.04); }
    .btn-primary { background: #274670; color: #fff; border: none; border-radius: 0.625rem; padding: 0.7rem 2rem; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: background 0.15s; }
    .btn-primary:hover { background: #1f3a5c; }
    .btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 0.625rem; padding: 0.7rem 2rem; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: background 0.15s; }
    .btn-secondary:hover { background: #e5e7eb; }
    .btn-submit { background: #C8102E; color: #fff; border: none; border-radius: 0.625rem; padding: 0.7rem 2.5rem; font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: background 0.15s; }
    .btn-submit:hover { background: #a50d27; }
    .btn-submit:disabled { background: #9ca3af; cursor: not-allowed; }

    /* Loading overlay */
    #submit-overlay {
        display: none;
        position: fixed; inset: 0; z-index: 9999;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(3px);
        align-items: center; justify-content: center;
    }
    #submit-overlay.active { display: flex; }
    .spinner {
        width: 48px; height: 48px;
        border: 4px solid rgba(255,255,255,0.25);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">

    <!-- Page Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center mb-4">
            <img src="{{ asset('images/protege-mindef-logo.png') }}" alt="Protege MINDEF" class="h-16 w-auto drop-shadow-[0_8px_20px_rgba(39,70,112,0.16)]">
        </div>
        <h1 class="text-2xl font-bold text-slate-900">{{ __('portal.register_page_title') }}</h1>
        <p class="text-slate-500 text-sm mt-1">{{ __('portal.register_subtitle') }}</p>
    </div>

    <!-- Validation Errors Banner -->
    @if($errors->any())
    <div id="error-banner" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-red-700 font-semibold text-sm mb-1">{{ __('portal.error_fix_notice') }}</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li class="text-red-600 text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Step Progress Indicator -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <div class="flex items-center justify-between" id="step-progress">
            @php
                $steps = [
                    ['label' => __('portal.step_personal'),    'num' => 1],
                    ['label' => __('portal.step_background'),  'num' => 2],
                    ['label' => __('portal.step_academic'),    'num' => 3],
                    ['label' => __('portal.step_preferences'), 'num' => 4],
                    ['label' => __('portal.step_documents'),   'num' => 5],
                ];
            @endphp
            @foreach($steps as $i => $step)
                <div class="flex items-center {{ $i < count($steps) - 1 ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center">
                        <div class="step-indicator w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all" id="step-circle-{{ $step['num'] }}">
                            {{ $step['num'] }}
                        </div>
                        <span class="text-xs text-slate-500 mt-1 hidden sm:block text-center leading-tight" style="max-width:70px">{{ $step['label'] }}</span>
                    </div>
                    @if($i < count($steps) - 1)
                        <div class="step-line flex-1 h-0.5 mx-2 transition-all" id="step-line-{{ $step['num'] }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('portal.register.store') }}" enctype="multipart/form-data" id="reg-form">
        @csrf

        <!-- ===== STEP 1: PERSONAL INFO ===== -->
        <div class="step-panel active" id="panel-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4">
                <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] px-6 py-4">
                    <h2 class="text-white font-bold text-base">{{ __('portal.step1_title') }}</h2>
                    <p class="text-blue-200 text-xs mt-0.5">{{ __('portal.step1_subtitle') }}</p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="form-label">{{ __('common.full_name') }} <span class="text-red-500">*</span> <span class="font-normal text-slate-400">{{ __('portal.full_name_hint') }}</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}"
                               class="form-input @error('full_name') error @enderror"
                               placeholder="{{ __('portal.full_name_placeholder') }}">
                        @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">{{ __('common.ic_passport_no') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="ic_passport_no" value="{{ old('ic_passport_no') }}"
                                   class="form-input @error('ic_passport_no') error @enderror"
                                   placeholder="{{ __('portal.ic_placeholder') }}" maxlength="20">
                            @error('ic_passport_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">{{ __('common.email') }} <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-input @error('email') error @enderror"
                                   placeholder="{{ __('portal.email_placeholder') }}">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">{{ __('portal.gender_label') }} <span class="text-red-500">*</span></label>
                        <div class="flex gap-3">
                            <label class="radio-card flex-1">
                                <input type="radio" name="gender" value="Lelaki" class="sr-only" {{ old('gender') === 'Lelaki' ? 'checked' : '' }}>
                                <div class="radio-inner border-2 border-gray-200 rounded-xl p-3 flex items-center gap-2 text-sm font-medium text-slate-700">
                                    <span class="text-lg">♂</span> {{ __('portal.male') }}
                                </div>
                            </label>
                            <label class="radio-card flex-1">
                                <input type="radio" name="gender" value="Perempuan" class="sr-only" {{ old('gender') === 'Perempuan' ? 'checked' : '' }}>
                                <div class="radio-inner border-2 border-gray-200 rounded-xl p-3 flex items-center gap-2 text-sm font-medium text-slate-700">
                                    <span class="text-lg">♀</span> {{ __('portal.female') }}
                                </div>
                            </label>
                        </div>
                        @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">{{ __('common.date_of_birth') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                   class="form-input @error('date_of_birth') error @enderror">
                            @error('date_of_birth') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">{{ __('common.phone_no') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   class="form-input @error('phone') error @enderror"
                                   placeholder="{{ __('portal.phone_placeholder') }}" maxlength="20">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">{{ __('portal.address_label') }} <span class="text-red-500">*</span></label>
                        <textarea name="address" rows="3"
                                  class="form-input @error('address') error @enderror"
                                  placeholder="{{ __('portal.address_placeholder') }}">{{ old('address') }}</textarea>
                        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="button" class="btn-primary" onclick="goToStep(2)">{{ __('portal.next') }} &rarr;</button>
            </div>
        </div>

        <!-- ===== STEP 2: BACKGROUND ===== -->
        <div class="step-panel" id="panel-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4">
                <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] px-6 py-4">
                    <h2 class="text-white font-bold text-base">{{ __('portal.step2_title') }}</h2>
                    <p class="text-blue-200 text-xs mt-0.5">{{ __('portal.step2_subtitle') }}</p>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="form-label">{{ __('portal.you_are_label') }} <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            @php
                                $bgTypes = [
                                    'anak_atm'        => __('portal.bg_atm_full'),
                                    'anak_veteran_atm' => __('portal.bg_veteran_full'),
                                    'anak_awam_mindef' => __('portal.bg_mindef_full'),
                                ];
                            @endphp
                            @foreach($bgTypes as $val => $label)
                            <label class="radio-card block">
                                <input type="radio" name="background_type" value="{{ $val }}" class="sr-only"
                                       {{ old('background_type') === $val ? 'checked' : '' }}>
                                <div class="radio-inner border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 cursor-pointer">
                                    {{ $label }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('background_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">{{ __('portal.guardian_full_name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="guardian_name" value="{{ old('guardian_name') }}"
                               class="form-input @error('guardian_name') error @enderror"
                               placeholder="{{ __('portal.guardian_full_name_placeholder') }}">
                        @error('guardian_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">{{ __('portal.guardian_ic_label') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="guardian_ic" value="{{ old('guardian_ic') }}"
                                   class="form-input @error('guardian_ic') error @enderror"
                                   placeholder="{{ __('portal.guardian_ic_placeholder') }}" maxlength="20">
                            @error('guardian_ic') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">{{ __('portal.guardian_military_label') }} <span class="text-slate-400 font-normal">{{ __('portal.guardian_military_hint') }}</span></label>
                            <input type="text" name="guardian_military_no" value="{{ old('guardian_military_no') }}"
                                   class="form-input @error('guardian_military_no') error @enderror"
                                   placeholder="{{ __('portal.guardian_military_placeholder') }}">
                            @error('guardian_military_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">{{ __('portal.guardian_relationship_label') }} <span class="text-red-500">*</span></label>
                        <select name="guardian_relationship" class="form-input @error('guardian_relationship') error @enderror">
                            <option value="">{{ __('portal.select_relationship') }}</option>
                            @php
                                $relationships = [
                                    'Bapa' => __('portal.rel_father'),
                                    'Ibu' => __('portal.rel_mother'),
                                    'Bapa Tiri' => __('portal.rel_step_father'),
                                    'Ibu Tiri' => __('portal.rel_step_mother'),
                                    'Penjaga Sah' => __('portal.rel_legal_guardian'),
                                ];
                            @endphp
                            @foreach($relationships as $rel => $label)
                                <option value="{{ $rel }}" {{ old('guardian_relationship') === $rel ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('guardian_relationship') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            <div class="flex justify-between">
                <button type="button" class="btn-secondary" onclick="goToStep(1)">&larr; {{ __('portal.previous') }}</button>
                <button type="button" class="btn-primary" onclick="goToStep(3)">{{ __('portal.next') }} &rarr;</button>
            </div>
        </div>

        <!-- ===== STEP 3: ACADEMIC ===== -->
        <div class="step-panel" id="panel-3">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4">
                <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] px-6 py-4">
                    <h2 class="text-white font-bold text-base">{{ __('portal.step3_title') }}</h2>
                    <p class="text-blue-200 text-xs mt-0.5">{{ __('portal.step3_subtitle') }}</p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="form-label">{{ __('portal.highest_qualification_label') }} <span class="text-red-500">*</span></label>
                        <select name="highest_qualification" class="form-input @error('highest_qualification') error @enderror">
                            <option value="">{{ __('portal.select_qualification') }}</option>
                            <option value="diploma"  {{ old('highest_qualification') === 'diploma'  ? 'selected' : '' }}>{{ __('common.qual_diploma') }}</option>
                            <option value="ijazah"   {{ old('highest_qualification') === 'ijazah'   ? 'selected' : '' }}>{{ __('portal.qual_ijazah') }}</option>
                            <option value="sarjana"  {{ old('highest_qualification') === 'sarjana'  ? 'selected' : '' }}>{{ __('portal.qual_sarjana') }}</option>
                            <option value="phd"      {{ old('highest_qualification') === 'phd'      ? 'selected' : '' }}>{{ __('portal.qual_phd') }}</option>
                            <option value="lain"     {{ old('highest_qualification') === 'lain'     ? 'selected' : '' }}>{{ __('portal.qual_lain') }}</option>
                        </select>
                        @error('highest_qualification') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="relative" x-data="autoSuggest('university')" @click.away="showSuggestions = false">
                        <label class="form-label">{{ __('common.university_institution') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="university" value="{{ old('university') }}"
                               class="form-input @error('university') error @enderror"
                               placeholder="{{ __('portal.university_placeholder') }}"
                               x-model="query" @input.debounce.300ms="fetchSuggestions()" @focus="fetchSuggestions()">
                        <ul x-show="showSuggestions && suggestions.length > 0" x-cloak
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            <template x-for="item in suggestions" :key="item">
                                <li @click="selectItem(item)" x-text="item"
                                    class="px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 cursor-pointer"></li>
                            </template>
                        </ul>
                        @error('university') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="relative" x-data="autoSuggest('programme')" @click.away="showSuggestions = false">
                        <label class="form-label">{{ __('common.programme_major') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="programme" value="{{ old('programme') }}"
                               class="form-input @error('programme') error @enderror"
                               placeholder="{{ __('portal.programme_placeholder') }}"
                               x-model="query" @input.debounce.300ms="fetchSuggestions()" @focus="fetchSuggestions()">
                        <ul x-show="showSuggestions && suggestions.length > 0" x-cloak
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            <template x-for="item in suggestions" :key="item">
                                <li @click="selectItem(item)" x-text="item"
                                    class="px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 cursor-pointer"></li>
                            </template>
                        </ul>
                        @error('programme') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">{{ __('common.graduation_year') }} <span class="text-red-500">*</span></label>
                            <select name="graduation_year" class="form-input @error('graduation_year') error @enderror">
                                <option value="">{{ __('portal.select_year') }}</option>
                                @foreach(range(date('Y') + 2, 1990) as $yr)
                                    <option value="{{ $yr }}" {{ old('graduation_year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                            @error('graduation_year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">{{ __('portal.cgpa_label') }} <span class="text-slate-400 font-normal">(0.00 – 4.00)</span></label>
                            <input type="number" name="cgpa" value="{{ old('cgpa') }}"
                                   class="form-input @error('cgpa') error @enderror"
                                   step="0.01" min="0" max="4" placeholder="{{ __('common.cgpa_placeholder_short') }}">
                            @error('cgpa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-between">
                <button type="button" class="btn-secondary" onclick="goToStep(2)">&larr; {{ __('portal.previous') }}</button>
                <button type="button" class="btn-primary" onclick="goToStep(4)">{{ __('portal.next') }} &rarr;</button>
            </div>
        </div>

        <!-- ===== STEP 4: PREFERENCES ===== -->
        <div class="step-panel" id="panel-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4">
                <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] px-6 py-4">
                    <h2 class="text-white font-bold text-base">{{ __('portal.step4_title') }}</h2>
                    <p class="text-blue-200 text-xs mt-0.5">{{ __('portal.step4_subtitle') }}</p>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="form-label">{{ __('portal.preferred_sectors_label') }} <span class="text-red-500">*</span> <span class="text-slate-400 font-normal">{{ __('portal.sectors_multi_select') }}</span></label>
                        @php
                            $sectors = [
                                'GLC'                          => __('portal.sector_glc'),
                                'industri_pertahanan'          => __('portal.sector_defence'),
                                'runcit_rantaian'              => __('portal.sector_retail'),
                                'perdagangan_kejuruteraan'     => __('portal.sector_trade_eng'),
                                'pentadbiran_hr_kewangan'      => __('portal.sector_admin_hr_finance'),
                                'lain_sektor'                  => __('portal.sector_other'),
                            ];
                            $oldSectors = old('preferred_sectors', []);
                        @endphp
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1">
                            @foreach($sectors as $val => $label)
                            <label class="check-card flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="preferred_sectors[]" value="{{ $val }}"
                                       class="w-4 h-4 text-[#274670] rounded"
                                       {{ in_array($val, $oldSectors) ? 'checked' : '' }}>
                                <span class="text-sm text-slate-700">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('preferred_sectors') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">{{ __('portal.preferred_locations_label') }} <span class="text-red-500">*</span> <span class="text-slate-400 font-normal">{{ __('portal.locations_multi_select') }}</span></label>
                        @php
                            $locations = ['Kuala Lumpur', 'Selangor', 'Johor', 'Pulau Pinang', 'Sabah / Sarawak', 'Lain-lain'];
                            $oldLocations = old('preferred_locations', []);
                        @endphp
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-1">
                            @foreach($locations as $loc)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="preferred_locations[]" value="{{ $loc }}"
                                       class="w-4 h-4 text-[#274670] rounded"
                                       {{ in_array($loc, $oldLocations) ? 'checked' : '' }}>
                                <span class="text-sm text-slate-700">{{ $loc }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('preferred_locations') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">{{ __('portal.currently_employed_q') }} <span class="text-red-500">*</span></label>
                        <div class="flex gap-3">
                            <label class="radio-card flex-1">
                                <input type="radio" name="currently_employed" value="1" class="sr-only"
                                       {{ old('currently_employed') === '1' ? 'checked' : '' }}>
                                <div class="radio-inner border-2 border-gray-200 rounded-xl p-3 text-center text-sm font-medium text-slate-700">{{ __('portal.yes') }}</div>
                            </label>
                            <label class="radio-card flex-1">
                                <input type="radio" name="currently_employed" value="0" class="sr-only"
                                       {{ old('currently_employed', '0') === '0' ? 'checked' : '' }}>
                                <div class="radio-inner border-2 border-gray-200 rounded-xl p-3 text-center text-sm font-medium text-slate-700">{{ __('portal.no') }}</div>
                            </label>
                        </div>
                        @error('currently_employed') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">{{ __('portal.available_start_date_label') }} <span class="text-slate-400 font-normal">{{ __('portal.available_start_date_hint') }}</span></label>
                        <input type="date" name="available_start_date" value="{{ old('available_start_date') }}"
                               class="form-input @error('available_start_date') error @enderror">
                        @error('available_start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            <div class="flex justify-between">
                <button type="button" class="btn-secondary" onclick="goToStep(3)">&larr; {{ __('portal.previous') }}</button>
                <button type="button" class="btn-primary" onclick="goToStep(5)">{{ __('portal.next') }} &rarr;</button>
            </div>
        </div>

        <!-- ===== STEP 5: DOCUMENTS & PDPA ===== -->
        <div class="step-panel" id="panel-5">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4">
                <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] px-6 py-4">
                    <h2 class="text-white font-bold text-base">{{ __('portal.step5_title') }}</h2>
                    <p class="text-blue-200 text-xs mt-0.5">{{ __('portal.step5_subtitle') }}</p>
                </div>
                <div class="p-6 space-y-5">

                    <!-- Documents -->
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800 mb-3 pb-2 border-b border-slate-100">{{ __('portal.supporting_docs_label') }}</h3>
                        <div class="space-y-3">
                            @php
                                $docFields = [
                                    ['field' => 'resume',        'label' => __('portal.doc_label_resume'),     'required' => true],
                                    ['field' => 'ic_copy',       'label' => __('portal.doc_label_ic'),         'required' => true],
                                    ['field' => 'transcript',    'label' => __('portal.doc_label_transcript'), 'required' => true],
                                    ['field' => 'military_card', 'label' => __('portal.doc_label_military'),   'required' => false],
                                ];
                            @endphp
                            @foreach($docFields as $doc)
                            <div>
                                <label class="form-label">
                                    {{ $doc['label'] }}
                                    @if($doc['required']) <span class="text-red-500">*</span>
                                    @else <span class="text-slate-400 font-normal">{{ __('portal.doc_if_applicable') }}</span>
                                    @endif
                                </label>
                                <div class="file-drop" id="drop-{{ $doc['field'] }}" onclick="document.getElementById('file-{{ $doc['field'] }}').click()">
                                    <input type="file" id="file-{{ $doc['field'] }}" name="{{ $doc['field'] }}"
                                           accept=".pdf" class="hidden"
                                           onchange="handleFileSelect(this, '{{ $doc['field'] }}')">
                                    <div id="drop-label-{{ $doc['field'] }}">
                                        <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-xs text-slate-500">{{ __('portal.click_to_select_pdf') }}</p>
                                    </div>
                                </div>
                                @error($doc['field']) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- PDPA Section -->
                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
                        <h3 class="text-sm font-bold text-[#1E3A5F] mb-3">{{ __('portal.pdpa_title') }}</h3>
                        <div class="text-xs text-slate-600 space-y-2 max-h-40 overflow-y-auto pr-1">
                            <p>{!! __('portal.pdpa_intro') !!}</p>
                            <p>{!! __('portal.pdpa_collection_purpose') !!}</p>
                            <p>{!! __('portal.pdpa_disclosure') !!}</p>
                            <p>{!! __('portal.pdpa_rights') !!}</p>
                            <p>{!! __('portal.pdpa_security') !!}</p>
                            <p>{{ __('portal.pdpa_acknowledgement') }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="pdpa_consent" value="1"
                                   class="mt-0.5 w-4 h-4 text-[#274670] rounded flex-shrink-0"
                                   {{ old('pdpa_consent') ? 'checked' : '' }}>
                            <span class="text-sm text-slate-700">
                                <strong>{{ __('portal.pdpa_title') }}:</strong> {{ __('portal.pdpa_checkbox_label') }} <span class="text-red-500">*</span>
                            </span>
                        </label>
                        @error('pdpa_consent') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">{{ __('common.declaration_signature') }} <span class="text-red-500">*</span></label>
                        <p class="text-xs text-slate-500 mb-2">{{ __('portal.signature_hint') }}</p>
                        <input type="text" name="declaration_signature" value="{{ old('declaration_signature') }}"
                               class="form-input @error('declaration_signature') error @enderror"
                               placeholder="{{ __('portal.signature_placeholder') }}">
                        @error('declaration_signature') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>
            </div>
            <div class="flex justify-between">
                <button type="button" class="btn-secondary" onclick="goToStep(4)">&larr; {{ __('portal.previous') }}</button>
                <button type="submit" class="btn-submit">
                    <svg class="w-4 h-4 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    {{ __('portal.submit_application') }}
                </button>
            </div>
        </div>

    </form>
</div>

{{-- Submit loading overlay --}}
<div id="submit-overlay" role="status" aria-live="polite">
    <div class="flex flex-col items-center gap-5 text-white text-center px-6">
        <div class="spinner"></div>
        <div>
            <p class="text-lg font-semibold">{{ __('portal.submitting_title') }}</p>
            <p class="text-sm text-white/70 mt-1">{{ __('portal.submitting_hint') }}</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
    const errorFields = {!! json_encode($errors->keys()) !!};

    const stepFieldMap = {
        1: ['full_name', 'ic_passport_no', 'email', 'gender', 'date_of_birth', 'phone', 'address'],
        2: ['background_type', 'guardian_name', 'guardian_ic', 'guardian_military_no', 'guardian_relationship'],
        3: ['highest_qualification', 'university', 'programme', 'graduation_year', 'cgpa'],
        4: ['preferred_sectors', 'preferred_locations', 'currently_employed', 'available_start_date'],
        5: ['resume', 'ic_copy', 'transcript', 'military_card', 'pdpa_consent', 'declaration_signature'],
    };

    let currentStep = 1;

    // Fields that MUST be filled per step
    const requiredFields = {
        1: ['full_name', 'ic_passport_no', 'email', 'gender', 'date_of_birth', 'phone', 'address'],
        2: ['background_type', 'guardian_name', 'guardian_ic', 'guardian_relationship'],
        3: ['highest_qualification', 'university', 'programme', 'graduation_year'],
        4: ['preferred_sectors', 'preferred_locations', 'currently_employed'],
        5: ['resume', 'ic_copy', 'transcript', 'pdpa_consent', 'declaration_signature'],
    };

    function validateStep(step) {
        const form = document.getElementById('reg-form');
        const fields = requiredFields[step] || [];
        let valid = true;
        let firstInvalid = null;

        // Clear previous client errors
        document.querySelectorAll('.step-error').forEach(el => el.remove());
        document.querySelectorAll('.client-error').forEach(el => el.classList.remove('client-error', 'error'));

        fields.forEach(name => {
            let fieldValid = false;
            let targetEl = null;

            // Check for checkbox groups (preferred_sectors[], preferred_locations[])
            const checkboxes = form.querySelectorAll(`[name="${name}[]"]`);
            if (checkboxes.length > 0) {
                fieldValid = Array.from(checkboxes).some(c => c.checked);
                targetEl = checkboxes[0].closest('div');
            } else {
                // Check for radio buttons
                const radios = form.querySelectorAll(`[name="${name}"][type="radio"]`);
                if (radios.length > 0) {
                    fieldValid = Array.from(radios).some(r => r.checked);
                    targetEl = radios[0].closest('div');
                } else {
                    // Single checkbox (pdpa_consent)
                    const checkbox = form.querySelector(`[name="${name}"][type="checkbox"]`);
                    if (checkbox) {
                        fieldValid = checkbox.checked;
                        targetEl = checkbox.closest('div');
                    } else {
                        // File inputs
                        const fileInput = form.querySelector(`[name="${name}"][type="file"]`);
                        if (fileInput) {
                            fieldValid = fileInput.files.length > 0;
                            targetEl = fileInput.closest('.file-drop') || fileInput.closest('div');
                        } else {
                            // Text/select/date inputs
                            const input = form.querySelector(`[name="${name}"]`);
                            if (input) {
                                fieldValid = input.value.trim() !== '';
                                targetEl = input;
                                if (!fieldValid) input.classList.add('client-error', 'error');
                            }
                        }
                    }
                }
            }

            if (!fieldValid) {
                valid = false;
                if (!firstInvalid && targetEl) firstInvalid = targetEl;

                // Show error message
                const container = targetEl ? (targetEl.closest('.space-y-5 > div') || targetEl.closest('div')) : null;
                if (container && !container.querySelector('.step-error')) {
                    const msg = document.createElement('p');
                    msg.className = 'step-error text-red-500 text-xs mt-1 font-medium';
                    msg.textContent = '{{ __("portal.field_required") }}';
                    container.appendChild(msg);
                }
            }
        });

        if (!valid && firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (firstInvalid.focus) firstInvalid.focus();
        }

        return valid;
    }

    function goToStep(step) {
        // Only validate when moving forward
        if (step > currentStep) {
            if (!validateStep(currentStep)) return;
        }
        document.getElementById('panel-' + currentStep).classList.remove('active');
        currentStep = step;
        document.getElementById('panel-' + currentStep).classList.add('active');
        updateProgress();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function updateProgress() {
        for (let i = 1; i <= 5; i++) {
            const circle = document.getElementById('step-circle-' + i);
            if (i < currentStep) {
                circle.className = 'step-indicator w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all done';
                circle.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
            } else if (i === currentStep) {
                circle.className = 'step-indicator w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all current';
                circle.innerHTML = i;
            } else {
                circle.className = 'step-indicator w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all pending';
                circle.innerHTML = i;
            }
            if (i < 5) {
                const line = document.getElementById('step-line-' + i);
                line.className = 'step-line flex-1 h-0.5 mx-2 transition-all ' + (i < currentStep ? 'done' : 'pending');
            }
        }
    }

    function handleFileSelect(input, fieldName) {
        const label = document.getElementById('drop-label-' + fieldName);
        const drop = document.getElementById('drop-' + fieldName);
        if (input.files && input.files[0]) {
            const file = input.files[0];
            label.innerHTML = `
                <svg class="w-6 h-6 mx-auto text-green-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs font-semibold text-green-600">${file.name}</p>
                <p class="text-xs text-slate-400">${(file.size / 1024 / 1024).toFixed(2)} MB</p>`;
            drop.classList.add('has-file');
        }
    }

    document.querySelectorAll('.radio-card input[type=radio]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const name = this.name;
            document.querySelectorAll(`.radio-card input[name="${name}"]`).forEach(function(r) {
                r.nextElementSibling.style.borderColor = '';
                r.nextElementSibling.style.background = '';
            });
            this.nextElementSibling.style.borderColor = '#274670';
            this.nextElementSibling.style.background = 'rgba(39,70,112,0.06)';
        });
        if (radio.checked) {
            radio.nextElementSibling.style.borderColor = '#274670';
            radio.nextElementSibling.style.background = 'rgba(39,70,112,0.06)';
        }
    });

    updateProgress();

    // Submit loading overlay
    document.getElementById('reg-form').addEventListener('submit', function () {
        var submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        document.getElementById('submit-overlay').classList.add('active');
    });

    if (hasErrors && errorFields.length > 0) {
        let targetStep = 1;
        for (let step = 1; step <= 5; step++) {
            const fields = stepFieldMap[step];
            if (errorFields.some(f => fields.includes(f) || f.startsWith('preferred_'))) {
                targetStep = step;
                break;
            }
        }
        if (targetStep !== 1) goToStep(targetStep);
    }

    // Alpine.js autocomplete suggestion component
    document.addEventListener('alpine:init', () => {
        Alpine.data('autoSuggest', (field) => ({
            query: '',
            suggestions: [],
            showSuggestions: false,
            async fetchSuggestions() {
                if (this.query.length < 1) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                    return;
                }
                try {
                    const res = await fetch(`{{ route('portal.suggestions') }}?field=${field}&term=${encodeURIComponent(this.query)}`);
                    this.suggestions = await res.json();
                    this.showSuggestions = true;
                } catch (e) {
                    this.suggestions = [];
                }
            },
            selectItem(item) {
                this.query = item;
                this.showSuggestions = false;
                const input = this.$el.querySelector('input[name="' + field + '"]');
                if (input) {
                    input.value = item;
                    input.dispatchEvent(new Event('input'));
                }
            },
            init() {
                const input = this.$el.querySelector('input[name="' + field + '"]');
                if (input && input.value) {
                    this.query = input.value;
                }
            }
        }));
    });
</script>
@endpush
