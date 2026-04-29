@extends('layouts.admin')

@section('title', __('common.review_application') . ' — ' . $talent->full_name)
@section('page-title', __('common.review_application'))

@section('content')

<!-- Back + Status Header -->
<div class="flex items-center justify-between mb-5">
    <a href="{{ route('admin.applications.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#274670] font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('common.back_to_applications_list') }}
    </a>
    <div>
        @if($talent->status === 'applied')
            <span class="bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1.5 rounded-full">{{ __('common.status.applied') }}</span>
        @elseif($talent->status === 'shortlisted')
            <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1.5 rounded-full">{{ __('common.status.shortlisted') }}</span>
        @elseif($talent->status === 'approved')
            <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1.5 rounded-full">{{ __('common.status.approved') }}</span>
        @elseif($talent->status === 'inactive')
            <span class="bg-red-100 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-full">{{ __('common.status.inactive') }}</span>
        @else
            <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1.5 rounded-full">{{ $talent->status }}</span>
        @endif
    </div>
</div>

<!-- Already reviewed notice -->
@if($talent->reviewed_at)
@php
    $reviewedAt = $talent->reviewed_at instanceof \Carbon\CarbonInterface
        ? $talent->reviewed_at
        : \Carbon\Carbon::parse($talent->reviewed_at);
@endphp
<div class="mb-5 p-4 {{ $talent->status === 'approved' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} border rounded-xl flex items-start gap-3">
    <svg class="w-5 h-5 {{ $talent->status === 'approved' ? 'text-green-500' : 'text-red-500' }} flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p class="text-sm font-semibold {{ $talent->status === 'approved' ? 'text-green-800' : 'text-red-800' }}">
            {{ __('messages.application_reviewed_on', ['status' => __('common.status.' . $talent->status), 'date' => $reviewedAt->format('d/m/Y H:i')]) }}
        </p>
        @if($talent->rejection_reason)
            <p class="text-xs mt-1 text-red-700">{{ __('common.rejection_reason_label') }} {{ $talent->rejection_reason }}</p>
        @endif
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <!-- Left Column: Detail Sections -->
    <div class="lg:col-span-2 space-y-5">

        <!-- 1. Maklumat Peribadi -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-[#1E3A5F] px-5 py-3">
                <h2 class="text-white font-semibold text-sm">{{ __('common.section_personal_info') }}</h2>
            </div>
            <div class="p-5 grid grid-cols-2 gap-x-6 gap-y-3">
                @php
                    $fields1 = [
                        [__('common.talent_code'), $talent->talent_code],
                        [__('common.full_name'), $talent->full_name],
                        [__('common.ic_passport_no'), $talent->ic_passport_no],
                        [__('common.email'), $talent->email],
                        [__('common.phone_no'), $talent->phone],
                        [__('portal.gender'), $talent->gender ? __('common.gender.' . $talent->gender) : null],
                        [__('common.date_of_birth'), $talent->date_of_birth?->format('d/m/Y')],
                    ];
                @endphp
                @foreach($fields1 as $f)
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">{{ $f[0] }}</p>
                    <p class="text-sm font-medium text-gray-800">{{ $f[1] ?? '—' }}</p>
                </div>
                @endforeach
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-0.5">{{ __('common.address') }}</p>
                    <p class="text-sm text-gray-800">{{ $talent->address ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- 2. Latar Belakang -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-[#1E3A5F] px-5 py-3">
                <h2 class="text-white font-semibold text-sm">{{ __('common.section_background') }}</h2>
            </div>
            <div class="p-5 grid grid-cols-2 gap-x-6 gap-y-3">
                @php
                    $bgTypeLabels = [
                        'anak_atm' => __('common.bg_atm_active'),
                        'anak_veteran_atm' => __('common.bg_atm_veteran'),
                        'anak_awam_mindef' => __('common.bg_mindef_civil'),
                    ];
                    $fields2 = [
                        [__('common.category'), $bgTypeLabels[$talent->background_type] ?? $talent->background_type],
                        [__('common.guardian_name'), $talent->guardian_name],
                        [__('common.guardian_ic'), $talent->guardian_ic],
                        [__('common.guardian_military_no'), $talent->guardian_military_no ?: '—'],
                        [__('common.guardian_relationship'), $talent->guardian_relationship],
                    ];
                @endphp
                @foreach($fields2 as $f)
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">{{ $f[0] }}</p>
                    <p class="text-sm font-medium text-gray-800">{{ $f[1] ?? '—' }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- 3. Maklumat Akademik -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-[#1E3A5F] px-5 py-3">
                <h2 class="text-white font-semibold text-sm">{{ __('common.section_academic_info') }}</h2>
            </div>
            <div class="p-5 grid grid-cols-2 gap-x-6 gap-y-3">
                @php
                    $qualLabels = [
                        'diploma' => __('common.qual_diploma'),
                        'ijazah' => __('common.qual_degree'),
                        'sarjana' => __('common.qual_masters'),
                        'phd' => __('common.qual_phd'),
                        'lain' => __('common.qual_other'),
                    ];
                    $fields3 = [
                        [__('common.highest_qualification'), $qualLabels[$talent->highest_qualification] ?? $talent->highest_qualification],
                        [__('common.university'), $talent->university],
                        [__('common.programme'), $talent->programme],
                        [__('common.graduation_year'), $talent->graduation_year],
                        ['CGPA', $talent->cgpa ? number_format($talent->cgpa, 2) : '—'],
                    ];
                @endphp
                @foreach($fields3 as $f)
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">{{ $f[0] }}</p>
                    <p class="text-sm font-medium text-gray-800">{{ $f[1] ?? '—' }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- 4. Pilihan Penempatan -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-[#1E3A5F] px-5 py-3">
                <h2 class="text-white font-semibold text-sm">{{ __('common.section_placement_prefs') }}</h2>
            </div>
            <div class="p-5 space-y-3">
                <div>
                    <p class="text-xs text-gray-400 mb-1.5">{{ __('portal.preferred_sectors') }}</p>
                    @if($talent->preferred_sectors)
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($talent->preferred_sectors as $sector)
                                <span class="bg-blue-50 text-blue-700 text-xs px-2.5 py-1 rounded-full font-medium">{{ $sector }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">—</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1.5">{{ __('portal.preferred_locations') }}</p>
                    @if($talent->preferred_locations)
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($talent->preferred_locations as $loc)
                                <span class="bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-full font-medium">{{ $loc }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">—</p>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">{{ __('common.employment_status') }}</p>
                        <p class="text-sm font-medium text-gray-800">{{ $talent->currently_employed ? __('common.employed') : __('common.not_employed') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">{{ __('common.available_start_date') }}</p>
                        <p class="text-sm font-medium text-gray-800">{{ $talent->available_start_date?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Dokumen -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-[#1E3A5F] px-5 py-3">
                <h2 class="text-white font-semibold text-sm">{{ __('common.section_documents') }}</h2>
            </div>
            <div class="p-5">
                @if($talent->documents->isEmpty())
                    <p class="text-sm text-gray-400">{{ __('messages.no_documents_uploaded') }}</p>
                @else
                    <div class="space-y-2">
                        @php
                            $docTypeLabels = [
                                'resume'       => __('common.doc_type_resume'),
                                'ic_copy'      => __('common.doc_type_ic'),
                                'transcript'   => __('common.doc_type_transcript'),
                                'military_card'=> __('common.doc_type_military'),
                            ];
                        @endphp
                        @foreach($talent->documents as $doc)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $docTypeLabels[$doc->document_type] ?? $doc->document_type }}</p>
                                    <p class="text-xs text-gray-400">{{ $doc->file_name }}</p>
                                </div>
                            </div>
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                               class="text-xs text-[#274670] font-semibold hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                {{ __('common.download') }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Right Column: Actions -->
    <div class="space-y-4">

        <!-- PDPA & Declaration -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">{{ __('common.pdpa_declaration') }}</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-2">
                    @if($talent->pdpa_consent)
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-green-700 font-medium">{{ __('common.pdpa_agreed') }}</span>
                    @else
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-red-500">{{ __('messages.pdpa_not_agreed') }}</span>
                    @endif
                </div>
                @if($talent->declaration_signature)
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">{{ __('common.declaration_signature') }}</p>
                    <p class="text-sm font-medium italic text-gray-700">"{{ $talent->declaration_signature }}"</p>
                </div>
                @endif
            </div>
        </div>

        @if(in_array($talent->status, ['applied', 'shortlisted']))
        <!-- Approve Action -->
        <div class="bg-white rounded-xl border border-green-200 shadow-sm overflow-hidden">
            <div class="bg-green-600 px-4 py-3">
                <h3 class="text-white font-semibold text-sm">{{ __('common.approve_application') }}</h3>
            </div>
            <form method="POST" action="{{ route('admin.applications.approve', $talent) }}" class="p-4">
                @csrf
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('common.notes_optional') }}</label>
                    <textarea name="notes" rows="3" placeholder="{{ __('messages.notes_placeholder') }}"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 resize-none">{{ old('notes') }}</textarea>
                </div>
                <button type="submit"
                        onclick="return confirm('{{ __('messages.confirm_approve_application') }}')"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold text-sm py-2.5 rounded-lg transition-colors">
                    {{ __('common.approve_application') }}
                </button>
            </form>
        </div>

        <!-- Reject Action -->
        <div class="bg-white rounded-xl border border-red-200 shadow-sm overflow-hidden">
            <div class="bg-[#C8102E] px-4 py-3">
                <h3 class="text-white font-semibold text-sm">{{ __('common.reject_application') }}</h3>
            </div>
            <form method="POST" action="{{ route('admin.applications.reject', $talent) }}" class="p-4">
                @csrf
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('common.rejection_reason') }} <span class="text-red-500">*</span></label>
                    <textarea name="rejection_reason" rows="3" required placeholder="{{ __('messages.rejection_reason_placeholder') }}"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 resize-none">{{ old('rejection_reason') }}</textarea>
                </div>
                <button type="submit"
                        onclick="return confirm('{{ __('messages.confirm_reject_application') }}')"
                        class="w-full bg-[#C8102E] hover:bg-red-700 text-white font-semibold text-sm py-2.5 rounded-lg transition-colors">
                    {{ __('common.reject_application') }}
                </button>
            </form>
        </div>
        @endif

        <!-- Timeline / Application Info -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">{{ __('common.application_info') }}</h3>
            <div class="space-y-2 text-xs text-gray-600">
                <div class="flex justify-between">
                    <span class="text-gray-400">{{ __('common.submission_date') }}</span>
                    <span class="font-medium">{{ $talent->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($talent->reviewed_at)
                <div class="flex justify-between">
                    <span class="text-gray-400">{{ __('common.reviewed_date') }}</span>
                    <span class="font-medium">{{ $talent->reviewed_at->format('d/m/Y H:i') }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-400">{{ __('common.talent_code') }}</span>
                    <span class="font-mono font-semibold text-[#274670]">{{ $talent->talent_code }}</span>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
