{{-- Quick Facts --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <h2 class="font-bold text-gray-800 mb-4">{{ __('portal.quick_facts') }}</h2>
    <div class="space-y-3">
        @if($talent->university)
        <div class="flex items-start gap-3">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            <div>
                <p class="text-xs text-gray-400">{{ __('common.university') }}</p>
                <p class="text-sm text-gray-700 font-medium">{{ $talent->university }}</p>
            </div>
        </div>
        @endif
        @if($talent->programme)
        <div class="flex items-start gap-3">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <div>
                <p class="text-xs text-gray-400">{{ __('common.programme') }}</p>
                <p class="text-sm text-gray-700 font-medium">{{ $talent->programme }}</p>
            </div>
        </div>
        @endif
        @if($talent->cgpa)
        <div class="flex items-start gap-3">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <div>
                <p class="text-xs text-gray-400">CGPA</p>
                <p class="text-sm text-[#1E3A5F] font-bold">{{ number_format($talent->cgpa, 2) }}</p>
            </div>
        </div>
        @endif
        @if($talent->graduation_year)
        <div class="flex items-start gap-3">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <div>
                <p class="text-xs text-gray-400">{{ __('portal.graduation_year') }}</p>
                <p class="text-sm text-gray-700 font-medium">{{ $talent->graduation_year }}</p>
            </div>
        </div>
        @endif
        @if($talent->negeri)
        <div class="flex items-start gap-3">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
            <div>
                <p class="text-xs text-gray-400">{{ __('portal.state') }}</p>
                <p class="text-sm text-gray-700 font-medium">{{ $talent->negeri }}</p>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Availability --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <h2 class="font-bold text-gray-800 mb-4">{{ __('portal.availability') }}</h2>
    <div class="space-y-3">
        <div class="flex items-center gap-2">
            @if($talent->currently_employed)
                <span class="w-2.5 h-2.5 rounded-full bg-orange-400 flex-shrink-0"></span>
                <span class="text-sm text-gray-600">{{ __('portal.currently_employed') }}</span>
            @else
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 flex-shrink-0 animate-pulse"></span>
                <span class="text-sm text-gray-600 font-medium">{{ __('portal.not_employed') }}</span>
            @endif
        </div>
        @if($talent->available_start_date)
        <div>
            <label class="text-xs text-gray-400 uppercase font-semibold tracking-wide">{{ __('portal.available_from') }}</label>
            <p class="text-gray-700 text-sm mt-0.5">{{ $talent->available_start_date->format('d M Y') }}</p>
        </div>
        @endif
        @if($talent->tarikh_mula || $talent->tarikh_tamat)
        <div class="pt-2 border-t border-gray-100">
            <label class="text-xs text-gray-400 uppercase font-semibold tracking-wide">{{ __('portal.programme_period') }}</label>
            <p class="text-gray-700 text-sm mt-0.5">
                {{ $talent->tarikh_mula?->format('d M Y') ?? '—' }} — {{ $talent->tarikh_tamat?->format('d M Y') ?? __('portal.ongoing') }}
            </p>
        </div>
        @endif
    </div>
</div>

{{-- Contact — masked --}}
@if($talent->email || $talent->phone)
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <h2 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
        <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        {{ __('portal.contact_info') }}
    </h2>
    <div class="space-y-2">
        @if($talent->email)
            @php
                $parts = explode('@', $talent->email);
                $name = $parts[0];
                $domain = $parts[1] ?? '';
                $masked = substr($name, 0, 3) . str_repeat('*', max(3, strlen($name) - 3)) . '@' . $domain;
            @endphp
            <div class="flex items-center gap-3 text-sm text-gray-700">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>{{ $masked }}</span>
            </div>
        @endif
        @if($talent->phone)
            @php $maskedPhone = substr($talent->phone, 0, 4) . str_repeat('*', max(4, strlen($talent->phone) - 7)) . substr($talent->phone, -3); @endphp
            <div class="flex items-center gap-3 text-sm text-gray-700">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <span>{{ $maskedPhone }}</span>
            </div>
        @endif
        <p class="text-xs text-gray-400 mt-2 italic">{{ __('portal.contact_masked_hint') }}</p>
    </div>
</div>
@endif

{{-- Personal Details --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <h2 class="font-bold text-gray-800 mb-4">{{ __('portal.personal_details') }}</h2>
    <div class="space-y-3">
        @if($talent->gender)
        <div>
            <label class="text-xs text-gray-400 uppercase font-semibold tracking-wide">{{ __('portal.gender') }}</label>
            <p class="text-gray-700 text-sm mt-0.5">{{ $talent->gender === 'male' ? __('common.gender.male') : __('common.gender.female') }}</p>
        </div>
        @endif
        @if($displayKategori)
        <div>
            <label class="text-xs text-gray-400 uppercase font-semibold tracking-wide">{{ __('portal.background') }}</label>
            <p class="text-gray-700 text-sm mt-0.5">{{ $displayKategori }}</p>
        </div>
        @endif
        @if($talent->talent_code)
        <div>
            <label class="text-xs text-gray-400 uppercase font-semibold tracking-wide">{{ __('portal.talent_id') }}</label>
            <p class="text-gray-700 text-sm mt-0.5 font-mono">{{ $talent->talent_code }}</p>
        </div>
        @endif
    </div>
</div>

{{-- YLTAT Badge --}}
<div class="bg-gradient-to-br from-[#1E3A5F] to-[#274670] rounded-xl p-5 text-white text-center shadow-md">
    <p class="text-blue-200 text-xs uppercase font-semibold tracking-wider mb-3">{{ __('portal.programme_badge') }}</p>
    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3 border-2 border-white/30">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
    </div>
    <p class="text-sm font-bold">{{ __('portal.registered_talent') }}</p>
    <p class="text-blue-300 text-xs mt-1">{{ __('portal.yltat_name') }}</p>
</div>
