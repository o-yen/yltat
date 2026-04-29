@extends('layouts.admin')

@section('title', __('common.add_new_talent'))
@section('page-title', __('common.add_new_talent'))

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.talents.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('common.back_to_talent_list') }}
        </a>
    </div>

    <form method="POST" action="{{ route('admin.talents.store') }}" enctype="multipart/form-data" x-data="{ tab: 'personal' }">
        @csrf

        <!-- Tab Navigation -->
        <div class="flex gap-1 mb-5 bg-gray-100 p-1 rounded-xl w-fit">
            <button type="button" @click="tab = 'personal'" :class="tab === 'personal' ? 'bg-white shadow-sm text-[#1E3A5F]' : 'text-gray-500'" class="px-4 py-2 rounded-lg text-sm font-medium transition-all">
                {{ __('common.tab_personal') }}
            </button>
            <button type="button" @click="tab = 'academic'" :class="tab === 'academic' ? 'bg-white shadow-sm text-[#1E3A5F]' : 'text-gray-500'" class="px-4 py-2 rounded-lg text-sm font-medium transition-all">
                {{ __('common.tab_academic') }}
            </button>
            <button type="button" @click="tab = 'skills'" :class="tab === 'skills' ? 'bg-white shadow-sm text-[#1E3A5F]' : 'text-gray-500'" class="px-4 py-2 rounded-lg text-sm font-medium transition-all">
                {{ __('common.tab_skills') }}
            </button>
            <button type="button" @click="tab = 'documents'" :class="tab === 'documents' ? 'bg-white shadow-sm text-[#1E3A5F]' : 'text-gray-500'" class="px-4 py-2 rounded-lg text-sm font-medium transition-all">
                {{ __('common.tab_documents') }}
            </button>
        </div>

        <!-- Personal Info Tab -->
        <div x-show="tab === 'personal'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('common.tab_personal') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.full_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('full_name') border-red-400 @enderror">
                    @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.ic_passport_no') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="ic_passport_no" value="{{ old('ic_passport_no') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('ic_passport_no') border-red-400 @enderror">
                    @error('ic_passport_no')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.date_of_birth') }}</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('portal.gender') }}</label>
                    <select name="gender" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('common.select') }}</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>{{ __('common.gender.male') }}</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>{{ __('common.gender.female') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.phone_no') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.address') }}</label>
                    <textarea name="address" rows="2"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.status_label') }} <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        @foreach(['applied', 'shortlisted', 'approved', 'assigned', 'in_progress', 'completed', 'alumni', 'inactive'] as $s)
                            <option value="{{ $s }}" {{ old('status') === $s ? 'selected' : '' }}>{{ __('common.status.' . $s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="public_visibility" id="public_visibility" value="1" {{ old('public_visibility', '1') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]">
                    <label for="public_visibility" class="text-sm text-gray-700">{{ __('common.show_on_public_portal') }}</label>
                </div>
            </div>
        </div>

        <!-- Academic Tab -->
        <div x-show="tab === 'academic'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="display:none">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('common.academic_info') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.university_institution') }}</label>
                    <input type="text" name="university" value="{{ old('university') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.programme_major') }}</label>
                    <input type="text" name="programme" value="{{ old('programme') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">CGPA</label>
                    <input type="number" name="cgpa" value="{{ old('cgpa') }}" min="0" max="4" step="0.01" inputmode="decimal"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                           placeholder="{{ __('common.cgpa_placeholder') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.graduation_year') }}</label>
                    <input type="number" name="graduation_year" value="{{ old('graduation_year') }}" min="1900" max="2100" step="1" inputmode="numeric" data-numeric="integer"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                           placeholder="{{ __('common.graduation_year_placeholder') }}">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.profile_summary') }}</label>
                    <textarea name="profile_summary" rows="4"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                              placeholder="{{ __('common.profile_summary_placeholder') }}">{{ old('profile_summary') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Skills Tab -->
        <div x-show="tab === 'skills'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="display:none">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('common.skills_and_notes') }}</h3>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.skills_comma_separated') }}</label>
                    <textarea name="skills_text" rows="4"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                              placeholder="{{ __('common.skills_placeholder_example') }}">{{ old('skills_text') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.admin_notes_internal') }}</label>
                    <textarea name="notes" rows="4"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                              placeholder="{{ __('common.admin_notes_placeholder') }}">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Documents Tab -->
        <div x-show="tab === 'documents'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="display:none">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('common.upload_documents') }}</h3>
            <div x-data="{ files: [] }">
                <input type="file" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                       @change="files = [...$event.target.files]"
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <p class="text-xs text-gray-400 mt-2">{{ __('common.accepted_formats') }}</p>

                <template x-for="(file, i) in files" :key="i">
                    <div class="mt-3 p-3 bg-gray-50 rounded-lg flex items-center gap-3">
                        <span x-text="file.name" class="flex-1 text-sm text-gray-700 truncate"></span>
                        <select :name="'document_types[' + i + ']'" class="px-2 py-1 border border-gray-300 rounded text-sm">
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

        <!-- Submit -->
        <div class="mt-5 flex items-center gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors shadow-sm">
                {{ __('common.save_talent_record') }}
            </button>
            <a href="{{ route('admin.talents.index') }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
