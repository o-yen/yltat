@extends('layouts.talent')

@section('title', __('talent.profile_title'))
@section('page-title', __('talent.profile_title'))

@section('content')
<div class="space-y-6 max-w-3xl">

    {{-- Profile header --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-start gap-5">

            {{-- Photo --}}
            <div class="flex-shrink-0" x-data="{ showPhotoForm: false }">
                <div class="relative group">
                    @if($talent->profile_photo)
                        <img src="{{ Storage::url($talent->profile_photo) }}"
                             class="w-24 h-24 rounded-full object-cover border-4 border-gray-100">
                    @else
                        <div class="w-24 h-24 rounded-full bg-[#1E3A5F] flex items-center justify-center text-white text-3xl font-bold border-4 border-gray-100">
                            {{ substr($talent->full_name, 0, 1) }}
                        </div>
                    @endif
                    <button @click="showPhotoForm = !showPhotoForm"
                            class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>

                {{-- Photo upload form --}}
                <div x-show="showPhotoForm" x-transition class="mt-3">
                    <form method="POST" action="{{ route('talent.profile.photo') }}" enctype="multipart/form-data"
                          class="flex flex-col gap-2">
                        @csrf
                        <input type="file" name="profile_photo" accept="image/jpg,image/jpeg,image/png,image/webp"
                               class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-[#1E3A5F] file:text-white hover:file:bg-[#274670]">
                        @error('profile_photo') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        <div class="flex gap-2">
                            <button type="submit" class="text-xs bg-[#1E3A5F] text-white px-3 py-1.5 rounded hover:bg-[#274670] transition-colors">{{ __('talent.upload_photo') }}</button>
                            <button type="button" @click="showPhotoForm = false" class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1.5">{{ __('talent.cancel') }}</button>
                        </div>
                    </form>
                </div>
                <p class="text-xs text-gray-400 mt-2 text-center">{{ __('talent.click_photo_to_change') }}</p>
            </div>

            {{-- Name & info --}}
            <div class="flex-1">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $talent->full_name }}</h2>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $talent->talent_code }}</p>
                        @php
                            $statusColors = ['approved' => 'bg-green-100 text-green-700', 'applied' => 'bg-yellow-100 text-yellow-700', 'rejected' => 'bg-red-100 text-red-700', 'inactive' => 'bg-gray-100 text-gray-600'];
                            $statusLabels = [
                                'approved' => __('talent.status_labels.approved'),
                                'applied' => __('talent.status_labels.applied'),
                                'rejected' => __('talent.status_labels.rejected'),
                                'inactive' => __('talent.status_labels.inactive'),
                            ];
                        @endphp
                        <span class="inline-block mt-2 text-xs px-2.5 py-1 rounded-full font-medium {{ $statusColors[$talent->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $statusLabels[$talent->status] ?? ucfirst($talent->status) }}
                        </span>
                    </div>
                    <a href="{{ route('talent.profile.edit') }}"
                       class="inline-flex items-center gap-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{ __('talent.profile_edit_title') }}
                    </a>
                </div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $talent->email }}
                    </div>
                    @if($talent->phone)
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $talent->phone }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Academic info --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('talent.academic_info') }}</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <dt class="text-xs text-gray-400">{{ __('talent.university_institution') }}</dt>
                <dd class="text-sm text-gray-800 mt-0.5">{{ $talent->university ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400">{{ __('talent.programme_study') }}</dt>
                <dd class="text-sm text-gray-800 mt-0.5">{{ $talent->programme ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400">{{ __('talent.highest_qualification') }}</dt>
                <dd class="text-sm text-gray-800 mt-0.5">{{ $talent->highest_qualification ? ucfirst($talent->highest_qualification) : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400">{{ __('talent.cgpa') }}</dt>
                <dd class="text-sm text-gray-800 mt-0.5">{{ $talent->cgpa ? number_format($talent->cgpa, 2) : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400">{{ __('talent.graduate_year') }}</dt>
                <dd class="text-sm text-gray-800 mt-0.5">{{ $talent->graduation_year ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Personal info --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('talent.personal_info') }}</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <dt class="text-xs text-gray-400">{{ __('talent.ic_passport_no') }}</dt>
                <dd class="text-sm text-gray-800 mt-0.5 font-mono tracking-wide">{{ $talent->ic_passport_no ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400">{{ __('talent.gender') }}</dt>
                <dd class="text-sm text-gray-800 mt-0.5">{{ $talent->gender ? __('common.gender.' . $talent->gender) : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400">{{ __('talent.date_of_birth') }}</dt>
                <dd class="text-sm text-gray-800 mt-0.5">{{ $talent->date_of_birth?->format('d M Y') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400">{{ __('talent.background') }}</dt>
                <dd class="text-sm text-gray-800 mt-0.5">{{ $talent->background_type ? str_replace('_', ' ', ucfirst($talent->background_type)) : '—' }}</dd>
            </div>
            @if($talent->address)
                <div class="sm:col-span-2">
                    <dt class="text-xs text-gray-400">{{ __('talent.address') }}</dt>
                    <dd class="text-sm text-gray-800 mt-0.5 whitespace-pre-line">{{ $talent->address }}</dd>
                </div>
            @endif
        </dl>
    </div>

    {{-- Skills & Summary --}}
    @if($talent->skills_text || $talent->profile_summary)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('talent.skills_summary') }}</h3>
            @if($talent->profile_summary)
                <div class="mb-4">
                    <p class="text-xs text-gray-400 mb-1">{{ __('talent.profile_summary') }}</p>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $talent->profile_summary }}</p>
                </div>
            @endif
            @if($talent->skills_text)
                <div>
                    <p class="text-xs text-gray-400 mb-2">{{ __('talent.skills') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(array_filter(array_map('trim', explode(',', $talent->skills_text))) as $skill)
                            <span class="bg-blue-50 text-blue-700 border border-blue-100 text-xs px-2.5 py-1 rounded-full">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
