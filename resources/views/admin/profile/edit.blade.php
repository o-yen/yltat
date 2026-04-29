@extends('layouts.admin')

@section('title', __('common.edit_profile'))
@section('page-title', __('common.edit_profile'))

@section('content')
<div class="max-w-2xl mx-auto">

    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Avatar card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
             x-data="{
                preview: '{{ $user->avatar ? Storage::url($user->avatar) : '' }}',
                remove: false,
                pick(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    this.preview = URL.createObjectURL(file);
                    this.remove = false;
                },
                removeAvatar() {
                    this.preview = '';
                    this.remove = true;
                    $refs.avatarInput.value = '';
                }
             }">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-5">{{ __('common.profile_photo') }}</h3>

            <div class="flex items-center gap-6">
                {{-- Preview --}}
                <div class="flex-shrink-0">
                    <template x-if="preview">
                        <img :src="preview" class="w-24 h-24 rounded-full object-cover ring-4 ring-gray-100 shadow">
                    </template>
                    <template x-if="!preview">
                        <div class="w-24 h-24 rounded-full bg-[#1E3A5F] flex items-center justify-center text-white text-3xl font-bold ring-4 ring-gray-100 shadow select-none">
                            {{ strtoupper(substr($user->full_name, 0, 1)) }}
                        </div>
                    </template>
                </div>

                {{-- Controls --}}
                <div class="space-y-2">
                    <label class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white text-sm font-medium rounded-lg cursor-pointer hover:bg-[#162d4a] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Upload Photo
                        <input type="file" name="avatar" accept="image/jpg,image/jpeg,image/png,image/webp"
                               x-ref="avatarInput" @change="pick($event)" class="hidden">
                    </label>
                    <p class="text-xs text-gray-400">{{ __('common.image_upload_hint') }}</p>
                    <input type="hidden" name="remove_avatar" :value="remove ? '1' : '0'">
                    @if($user->avatar)
                        <button type="button" @click="removeAvatar()" x-show="!remove"
                                class="text-xs text-red-500 hover:text-red-700 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Remove photo
                        </button>
                    @endif
                </div>
            </div>

            @error('avatar')
                <p class="mt-3 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Personal info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-5">{{ __('common.account_info') }}</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('common.full_name') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('full_name') border-red-400 @enderror">
                    @error('full_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('common.email') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('email') border-red-400 @enderror">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.phone_no') }}</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                           placeholder="{{ __('common.phone_placeholder_example') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('phone') border-red-400 @enderror">
                    @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.language_label') }}</label>
                    <div class="grid grid-cols-2 gap-3" x-data="{}">
                        @foreach(['ms' => '🇲🇾 Bahasa Melayu', 'en' => '🇬🇧 English'] as $code => $label)
                        <label class="flex items-center gap-3 cursor-pointer px-4 py-3 rounded-lg border-2 transition-colors
                            {{ old('language', $user->language) === $code ? 'border-[#1E3A5F] bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" name="language" value="{{ $code }}"
                                   {{ old('language', $user->language) === $code ? 'checked' : '' }}
                                   class="text-[#1E3A5F] focus:ring-[#1E3A5F]"
                                   onchange="document.querySelectorAll('[name=language]').forEach(r => {
                                       r.closest('label').classList.toggle('border-[#1E3A5F]', r.checked);
                                       r.closest('label').classList.toggle('bg-blue-50', r.checked);
                                       r.closest('label').classList.toggle('border-gray-200', !r.checked);
                                   })">
                            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Read-only info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">{{ __('common.account_link') }}</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">{{ __('common.role') }}</p>
                    <p class="text-sm font-medium text-gray-800">{{ $user->role?->role_name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">{{ __('common.member_since') }}</p>
                    <p class="text-sm font-medium text-gray-800">{{ $user->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.profile.show') }}"
               class="px-5 py-2.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-[#1E3A5F] text-white text-sm font-semibold rounded-lg hover:bg-[#162d4a] transition-colors shadow-sm">
                {{ __('common.save_profile') }}
            </button>
        </div>

    </form>
</div>
@endsection
