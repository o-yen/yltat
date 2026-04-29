@extends('layouts.admin')

@section('title', __('common.edit_talent') . ' - ' . $talent->full_name)
@section('page-title', __('common.edit_talent_profile'))

@php
    $backgroundOptions = [
        'anak_atm' => __('common.bg_atm_active'),
        'anak_veteran_atm' => __('common.bg_atm_veteran'),
        'anak_awam_mindef' => __('common.bg_mindef_civil'),
    ];

    // Kategori values as stored in DB (from Excel/seeder)
    $kategoriOptions = ['Anak ATM', 'Anak Veteran', 'Anak Awam MINDEF'];

    // Programme status values as stored in DB
    $statusAktifOptions = ['Aktif', 'Tamat', 'Berhenti Awal'];

    $qualificationOptions = [
        'diploma' => __('common.qual_diploma'),
        'ijazah' => __('common.qual_degree'),
        'sarjana' => __('common.qual_masters'),
        'phd' => __('common.qual_phd'),
        'lain' => __('common.qual_other'),
    ];

    $statusOptions = array_values(array_unique(array_filter([
        old('status', $talent->status),
        'Aktif',
        'Tamat',
        'Berhenti Awal',
        'applied',
        'shortlisted',
        'approved',
        'assigned',
        'in_progress',
        'completed',
        'alumni',
        'inactive',
    ])));
    $programmeStatusOptions = array_values(array_unique(array_filter([
        old('status_aktif', $talent->status_aktif),
        ...$statusAktifOptions,
        'applied',
        'approved',
        'assigned',
        'in_progress',
        'completed',
        'alumni',
        'inactive',
    ])));
    $absorptionOptions = array_values(array_unique(array_filter([
        old('status_penyerapan_6bulan', $talent->status_penyerapan_6bulan),
        'Diserap',
        'Tidak Diserap',
        'Belum Layak',
        'Dalam Proses',
    ])));
    $preferredSectors = old('preferred_sectors_text', implode(PHP_EOL, $talent->preferred_sectors ?? []));
    $preferredLocations = old('preferred_locations_text', implode(PHP_EOL, $talent->preferred_locations ?? []));
    $displayStatus = static fn (string $status): string => str_contains($status, '_') ? __('common.status.' . $status) : $status;
    $derivedBackgroundType = $talent->background_type ?: match ($talent->kategori) {
        'Anak ATM' => 'anak_atm',
        'Anak Veteran', 'Anak Veteran ATM' => 'anak_veteran_atm',
        'Anak Awam MINDEF', 'Anak Kakitangan Awam MINDEF' => 'anak_awam_mindef',
        default => null,
    };
    $derivedQualificationCode = $talent->highest_qualification ?: match (true) {
        blank($talent->kelayakan) => null,
        str_contains(strtolower($talent->kelayakan), 'diploma') => 'diploma',
        str_contains(strtolower($talent->kelayakan), 'sarjana') => 'sarjana',
        str_contains(strtolower($talent->kelayakan), 'phd') => 'phd',
        str_contains(strtolower($talent->kelayakan), 'ijazah') => 'ijazah',
        default => 'lain',
    };
@endphp

@section('content')
<div class="max-w-6xl" x-data="{ tab: 'personal' }">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <a href="{{ route('admin.talents.show', $talent) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('common.back_to_profile') }}
            </a>
            <h2 class="mt-3 text-xl font-bold text-gray-900">{{ $talent->full_name }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('common.profile_overview') }}</p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-500">{{ __('common.talent_code') }}</p>
                <p class="mt-1 font-mono text-sm font-bold text-blue-700">{{ $talent->talent_code }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('common.id_graduan') }}</p>
                <p class="mt-1 font-mono text-sm font-bold text-gray-800">{{ $talent->id_graduan ?: '—' }}</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">{{ __('messages.validation_failed') ?? 'Validation failed.' }}</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.talents.update', $talent) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-5 overflow-x-auto">
            <div class="inline-flex min-w-full gap-1 rounded-xl bg-gray-100 p-1 lg:min-w-0">
                <button type="button" @click="tab = 'personal'" :class="tab === 'personal' ? 'bg-white text-[#1E3A5F] shadow-sm' : 'text-gray-500'" class="rounded-lg px-4 py-2 text-sm font-medium transition">{{ __('common.tab_personal') }}</button>
                <button type="button" @click="tab = 'background'" :class="tab === 'background' ? 'bg-white text-[#1E3A5F] shadow-sm' : 'text-gray-500'" class="rounded-lg px-4 py-2 text-sm font-medium transition">{{ __('common.tab_background') }}</button>
                <button type="button" @click="tab = 'academic'" :class="tab === 'academic' ? 'bg-white text-[#1E3A5F] shadow-sm' : 'text-gray-500'" class="rounded-lg px-4 py-2 text-sm font-medium transition">{{ __('common.tab_academic') }}</button>
                <button type="button" @click="tab = 'protege'" :class="tab === 'protege' ? 'bg-white text-[#1E3A5F] shadow-sm' : 'text-gray-500'" class="rounded-lg px-4 py-2 text-sm font-medium transition">{{ __('common.tab_protege') }}</button>
                <button type="button" @click="tab = 'documents'" :class="tab === 'documents' ? 'bg-white text-[#1E3A5F] shadow-sm' : 'text-gray-500'" class="rounded-lg px-4 py-2 text-sm font-medium transition">{{ __('common.tab_documents_certs') }}</button>
                <button type="button" @click="tab = 'notes'" :class="tab === 'notes' ? 'bg-white text-[#1E3A5F] shadow-sm' : 'text-gray-500'" class="rounded-lg px-4 py-2 text-sm font-medium transition">{{ __('common.tab_admin_notes') }}</button>
            </div>
        </div>

        <div x-show="tab === 'personal'" class="space-y-5">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-5 text-sm font-semibold text-gray-800">{{ __('common.record_identifiers') }}</h3>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.id_graduan') }}</label>
                        <input type="text" name="id_graduan" value="{{ old('id_graduan', $talent->id_graduan) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.talent_code') }}</label>
                        <input type="text" value="{{ $talent->talent_code }}" disabled class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-500">
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-5 text-sm font-semibold text-gray-800">{{ __('common.tab_personal') }}</h3>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="md:col-span-2" x-data="{ preview: '{{ $talent->profile_photo ? asset("storage/{$talent->profile_photo}") : '' }}' }">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.profile_photo') }}</label>
                        <div class="flex items-center gap-4">
                            <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl border border-gray-200 bg-gray-100">
                                <template x-if="preview">
                                    <img :src="preview" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!preview">
                                    <span class="text-2xl font-bold text-gray-400">{{ substr($talent->full_name, 0, 1) }}</span>
                                </template>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview" class="w-full cursor-pointer text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-[#1E3A5F] file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-[#152c47]">
                                <p class="mt-1 text-xs text-gray-400">{{ __('common.image_upload_hint') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.full_name') }} *</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $talent->full_name) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.ic_passport_no') }} *</label>
                        <input type="text" name="ic_passport_no" value="{{ old('ic_passport_no', $talent->ic_passport_no) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.date_of_birth') }}</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $talent->date_of_birth?->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('portal.gender') }}</label>
                        <select name="gender" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                            <option value="">{{ __('common.select') }}</option>
                            <option value="male" {{ old('gender', $talent->gender) === 'male' ? 'selected' : '' }}>{{ __('common.gender.male') }}</option>
                            <option value="female" {{ old('gender', $talent->gender) === 'female' ? 'selected' : '' }}>{{ __('common.gender.female') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.qualification') }}</label>
                        <input type="text" name="kelayakan" value="{{ old('kelayakan', $talent->kelayakan) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $talent->email) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.phone_no') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $talent->phone) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.state') }}</label>
                        <input type="text" name="negeri" value="{{ old('negeri', $talent->negeri) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.address') }}</label>
                        <textarea name="address" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('address', $talent->address) }}</textarea>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.status_label') }} *</label>
                        <select name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                            @foreach($statusOptions as $statusOption)
                                <option value="{{ $statusOption }}" {{ old('status', $talent->status) === $statusOption ? 'selected' : '' }}>{{ $displayStatus($statusOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-3 pt-8">
                        <input type="checkbox" name="public_visibility" id="public_visibility" value="1" {{ old('public_visibility', $talent->public_visibility) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]">
                        <label for="public_visibility" class="text-sm text-gray-700">{{ __('common.show_on_public_portal') }}</label>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="tab === 'background'" x-cloak class="space-y-5" style="display: none;">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-5 text-sm font-semibold text-gray-800">{{ __('common.section_background') }}</h3>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.category') }}</label>
                        <select name="background_type" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                            <option value="">{{ __('common.select') }}</option>
                            @foreach($backgroundOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('background_type', $derivedBackgroundType) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.guardian_name') }}</label>
                        <input type="text" name="guardian_name" value="{{ old('guardian_name', $talent->guardian_name) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.guardian_ic') }}</label>
                        <input type="text" name="guardian_ic" value="{{ old('guardian_ic', $talent->guardian_ic) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.guardian_military_no') }}</label>
                        <input type="text" name="guardian_military_no" value="{{ old('guardian_military_no', $talent->guardian_military_no) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.guardian_relationship') }}</label>
                        <input type="text" name="guardian_relationship" value="{{ old('guardian_relationship', $talent->guardian_relationship) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.highest_qualification') }}</label>
                        <select name="highest_qualification" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                            <option value="">{{ __('common.select') }}</option>
                            @foreach($qualificationOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('highest_qualification', $derivedQualificationCode) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-5 text-sm font-semibold text-gray-800">{{ __('common.application_preferences') }}</h3>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('portal.preferred_sectors') }}</label>
                        <textarea name="preferred_sectors_text" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ $preferredSectors }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">{{ __('common.comma_or_newline_hint') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('portal.preferred_locations') }}</label>
                        <textarea name="preferred_locations_text" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ $preferredLocations }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">{{ __('common.comma_or_newline_hint') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="currently_employed" id="currently_employed" value="1" {{ old('currently_employed', $talent->currently_employed) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]">
                        <label for="currently_employed" class="text-sm text-gray-700">{{ __('common.employed') }}</label>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.available_start_date') }}</label>
                        <input type="date" name="available_start_date" value="{{ old('available_start_date', $talent->available_start_date?->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                </div>
            </div>
        </div>

        <div x-show="tab === 'academic'" x-cloak class="space-y-5" style="display: none;">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-5 text-sm font-semibold text-gray-800">{{ __('common.academic_info') }}</h3>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.university_institution') }}</label>
                        <input type="text" name="university" value="{{ old('university', $talent->university) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.programme_major') }}</label>
                        <input type="text" name="programme" value="{{ old('programme', $talent->programme) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.cgpa') }}</label>
                        <input type="number" name="cgpa" value="{{ old('cgpa', $talent->cgpa) }}" min="0" max="4" step="0.01" inputmode="decimal" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]" placeholder="{{ __('common.cgpa_placeholder') }}">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.graduation_year') }}</label>
                        <input type="number" name="graduation_year" value="{{ old('graduation_year', $talent->graduation_year) }}" min="1900" max="2100" step="1" inputmode="numeric" data-numeric="integer" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]" placeholder="{{ __('common.graduation_year_placeholder') }}">
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.profile_summary') }}</label>
                        <textarea name="profile_summary" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]" placeholder="{{ __('common.profile_summary_placeholder') }}">{{ old('profile_summary', $talent->profile_summary) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="tab === 'protege'" x-cloak class="space-y-5" style="display: none;">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-2 text-sm font-semibold text-gray-800">{{ __('common.protege_programme') }}</h3>
                <p class="mb-5 text-xs text-gray-400">{{ __('common.placement_linking_note') }}</p>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.category') }}</label>
                        <select name="kategori" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                            <option value="">{{ __('common.select') }}</option>
                            @foreach($kategoriOptions as $option)
                                <option value="{{ $option }}" {{ old('kategori', $talent->kategori) === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.six_month_absorption_status') }}</label>
                        <select name="status_penyerapan_6bulan" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                            <option value="">{{ __('common.select') }}</option>
                            @foreach($absorptionOptions as $option)
                                <option value="{{ $option }}" {{ old('status_penyerapan_6bulan', $talent->status_penyerapan_6bulan) === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.implementing_company') }}</label>
                        <select name="id_pelaksana" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                            <option value="">{{ __('common.select_pelaksana_account_link') }}</option>
                            @foreach($pelaksanaOptions as $pelaksana)
                                <option value="{{ $pelaksana->id_pelaksana }}" {{ old('id_pelaksana', $talent->id_pelaksana) === $pelaksana->id_pelaksana ? 'selected' : '' }}>
                                    {{ $pelaksana->id_pelaksana }} — {{ $pelaksana->nama_syarikat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.placement_company') }}</label>
                        <select name="id_syarikat_penempatan" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                            <option value="">{{ __('common.select_company') }}</option>
                            @foreach($penempatanOptions as $penempatan)
                                <option value="{{ $penempatan->id_syarikat }}" {{ old('id_syarikat_penempatan', $talent->id_syarikat_penempatan) === $penempatan->id_syarikat ? 'selected' : '' }}>
                                    {{ $penempatan->id_syarikat }} — {{ $penempatan->nama_syarikat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.job_title') }}</label>
                        <input type="text" name="jawatan" value="{{ old('jawatan', $talent->jawatan) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.current_programme_status') }}</label>
                        <select name="status_aktif" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                            <option value="">{{ __('common.select') }}</option>
                            @foreach($programmeStatusOptions as $statusOption)
                                <option value="{{ $statusOption }}" {{ old('status_aktif', $talent->status_aktif) === $statusOption ? 'selected' : '' }}>{{ $displayStatus($statusOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.start_date') }}</label>
                        <input type="date" name="tarikh_mula" value="{{ old('tarikh_mula', $talent->tarikh_mula?->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.end_date') }}</label>
                        <input type="date" name="tarikh_tamat" value="{{ old('tarikh_tamat', $talent->tarikh_tamat?->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                </div>
            </div>

            {{-- Placement Details (merged from old Placement module) --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-2 text-sm font-semibold text-gray-800">{{ __('common.placement_details') ?? 'Placement Details' }}</h3>
                <p class="mb-5 text-xs text-gray-400">{{ __('common.placement_details_note') ?? 'Supervisor, stipend, and department information' }}</p>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.department') ?? 'Department' }}</label>
                        <input type="text" name="department" value="{{ old('department', $talent->department) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.programme_type') ?? 'Programme Type' }}</label>
                        <input type="text" name="programme_type" value="{{ old('programme_type', $talent->programme_type) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]" placeholder="{{ __('common.programme_type_placeholder') }}">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.supervisor_name') ?? 'Supervisor Name' }}</label>
                        <input type="text" name="supervisor_name" value="{{ old('supervisor_name', $talent->supervisor_name) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.supervisor_email') ?? 'Supervisor Email' }}</label>
                        <input type="email" name="supervisor_email" value="{{ old('supervisor_email', $talent->supervisor_email) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.duration_months') ?? 'Duration (Months)' }}</label>
                        <input type="number" name="duration_months" value="{{ old('duration_months', $talent->duration_months) }}" min="1" max="36" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.monthly_stipend') ?? 'Monthly Stipend (RM)' }}</label>
                        <input type="number" name="monthly_stipend" value="{{ old('monthly_stipend', $talent->monthly_stipend) }}" min="0" step="0.01" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.additional_cost') ?? 'Additional Cost (RM)' }}</label>
                        <input type="number" name="additional_cost" value="{{ old('additional_cost', $talent->additional_cost) }}" min="0" step="0.01" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                </div>
            </div>
        </div>

        <div x-show="tab === 'documents'" x-cloak class="space-y-5" style="display: none;">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-5 text-sm font-semibold text-gray-800">{{ __('common.upload_documents') }}</h3>
                <div x-data="{ files: [] }">
                    <input type="file" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" @change="files = [...$event.target.files]" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    <p class="mt-2 text-xs text-gray-400">{{ __('common.accepted_formats') }}</p>

                    <template x-for="(file, i) in files" :key="i">
                        <div class="mt-3 flex items-center gap-3 rounded-lg bg-gray-50 p-3">
                            <span x-text="file.name" class="flex-1 truncate text-sm text-gray-700"></span>
                            <select :name="'document_types[' + i + ']'" class="rounded border border-gray-300 px-2 py-1 text-sm">
                                <option value="resume">{{ __('common.doc_types.resume') }}</option>
                                <option value="transcript">{{ __('common.doc_types.transcript') }}</option>
                                <option value="ic">{{ __('common.doc_types.ic') }}</option>
                                <option value="cert">{{ __('common.doc_types.cert') }}</option>
                                <option value="other">{{ __('common.doc_types.other') }}</option>
                            </select>
                        </div>
                    </template>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-5 text-sm font-semibold text-gray-800">{{ __('common.uploaded_documents') }}</h3>
                <div class="space-y-3">
                    @forelse($talent->documents as $doc)
                        <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $doc->file_name }}</p>
                                <p class="text-xs text-gray-400">{{ $doc->document_type }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-sm font-medium text-blue-700 hover:underline">{{ __('common.download') }}</a>
                                <button type="submit" form="delete-doc-{{ $doc->id }}"
                                        onclick="return confirm('{{ __('messages.confirm_delete_document') }}')"
                                        class="text-sm font-medium text-red-600 hover:underline">{{ __('common.delete') }}</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">{{ __('messages.no_documents_uploaded') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800">{{ __('common.existing_certifications') }}</h3>
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">{{ $talent->certifications->count() }} {{ __('common.total') }}</span>
                </div>

                <div class="space-y-3">
                    @if($talent->certifications->count() > 0)
                        @foreach($talent->certifications as $cert)
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="text-sm font-medium text-gray-800">{{ $cert->certification_name }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $cert->issuer ?: '—' }}
                                @if($cert->issue_date)
                                    · {{ $cert->issue_date->format('d/m/Y') }}
                                @endif
                                @if($cert->expiry_date)
                                    → {{ $cert->expiry_date->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-400">{{ __('common.no_certifications_added') }}</p>
                    @endif
                </div>

                <div class="mt-6 border-t border-gray-100 pt-6">
                    <h4 class="mb-4 text-sm font-semibold text-gray-800">{{ __('common.add_new_cert') }}</h4>
                    <div x-data="{ certs: [{}] }">
                        <template x-for="(cert, i) in certs" :key="i">
                            <div class="mb-4 grid grid-cols-1 gap-4 rounded-lg bg-gray-50 p-4 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-xs font-medium text-gray-600">{{ __('common.cert_name') }}</label>
                                    <input type="text" :name="'cert_name[' + i + ']'" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-600">{{ __('common.cert_issuer') }}</label>
                                    <input type="text" :name="'cert_issuer[' + i + ']'" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-600">{{ __('common.cert_issue_date') }}</label>
                                    <input type="date" :name="'cert_issue_date[' + i + ']'" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-600">{{ __('common.cert_expiry_date') }}</label>
                                    <input type="date" :name="'cert_expiry_date[' + i + ']'" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                </div>
                            </div>
                        </template>
                        <button type="button" @click="certs.push({})" class="text-sm font-medium text-blue-700 hover:text-blue-900">{{ __('common.add_more_cert') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="tab === 'notes'" x-cloak class="space-y-5" style="display: none;">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-5 text-sm font-semibold text-gray-800">{{ __('common.tab_admin_notes') }}</h3>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.skills_comma_separated') }}</label>
                        <textarea name="skills_text" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]" placeholder="{{ __('common.skills_placeholder_example') }}">{{ old('skills_text', $talent->skills_text) }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.admin_notes_internal') }}</label>
                        <textarea name="notes" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]" placeholder="{{ __('common.admin_notes_placeholder') }}">{{ old('notes', $talent->notes) }}</textarea>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="pdpa_consent" id="pdpa_consent" value="1" {{ old('pdpa_consent', $talent->pdpa_consent) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]">
                        <label for="pdpa_consent" class="text-sm text-gray-700">{{ __('common.pdpa_agreed') }}</label>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.declaration_signature') }}</label>
                        <input type="text" name="declaration_signature" value="{{ old('declaration_signature', $talent->declaration_signature) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('common.rejection_reason') }}</label>
                        <textarea name="rejection_reason" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('rejection_reason', $talent->rejection_reason) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-[#1E3A5F] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#152c47]">
                {{ __('common.save_changes') }}
            </button>
            <a href="{{ route('admin.talents.show', $talent) }}" class="rounded-lg bg-gray-100 px-6 py-2.5 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-200">
                {{ __('messages.cancel') }}
            </a>
        </div>
    </form>

    {{-- Delete document forms outside main form to avoid nesting --}}
    @foreach($talent->documents as $doc)
        <form id="delete-doc-{{ $doc->id }}" method="POST"
              action="{{ route('admin.talents.delete-document', $doc) }}" style="display:none">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
</div>
@endsection
