@extends('layouts.admin')

@section('title', __('protege.log_edit_record') . ' - ' . $logbook->id_graduan)
@section('page-title', __('protege.log_edit_record'))

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.logbook.show', $logbook) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('protege.back_to_details') }}
        </a>
    </div>

    <div class="mb-4 inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg text-sm">
        <span class="font-mono font-bold">{{ $logbook->id_graduan }}</span>
        <span class="text-blue-400">|</span>
        <span>{{ $logbook->nama_graduan ?? '-' }}</span>
        <span class="text-blue-400">|</span>
        <span>{{ $logbook->bulan }} {{ $logbook->tahun }}</span>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: '{{ $errors->has('logbook_file') ? 'file' : 'details' }}' }">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-5 overflow-hidden">
            <div class="flex border-b border-gray-100">
                <button @click="tab = 'details'"
                        :class="tab === 'details' ? 'border-[#1E3A5F] text-[#1E3A5F] font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-5 py-3 text-sm border-b-2 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    {{ __('protege.log_info') }}
                </button>
                <button @click="tab = 'file'"
                        :class="tab === 'file' ? 'border-[#1E3A5F] text-[#1E3A5F] font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-5 py-3 text-sm border-b-2 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    {{ __('protege.log_file') }}
                    @if($logbook->link_file_logbook)
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    @endif
                </button>
            </div>
        </div>

        {{-- ═══ Tab 1: Logbook Details ═══ --}}
        <div x-show="tab === 'details'">
            <form method="POST" action="{{ route('admin.logbook.update', $logbook) }}">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-5">{{ __('protege.log_info') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.nama_graduan') }} <span class="text-red-500">*</span></label>
                            <select id="id_graduan" name="id_graduan" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                <option value="">-- {{ __('protege.select_graduan') }} --</option>
                                @foreach($talents as $t)
                                    <option value="{{ $t->id_graduan }}" data-full-name="{{ $t->full_name }}" data-syarikat-id="{{ $t->id_syarikat_penempatan }}" data-syarikat-name="{{ $t->syarikatPenempatan?->nama_syarikat }}" {{ old('id_graduan', $logbook->id_graduan) == $t->id_graduan ? 'selected' : '' }}>
                                        {{ $t->full_name }} ({{ $t->id_graduan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.nama_graduan') }} <span class="text-red-500">*</span></label>
                            <input id="nama_graduan" type="text" name="nama_graduan" value="{{ old('nama_graduan', $logbook->nama_graduan) }}" required
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.penempatan') }}</label>
                            <select id="id_syarikat" name="id_syarikat" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                <option value="">{{ __('protege.select_company') }}</option>
                                @foreach($penempatan as $s)
                                    <option value="{{ $s->id_syarikat }}" {{ old('id_syarikat', $logbook->id_syarikat) == $s->id_syarikat ? 'selected' : '' }}>{{ $s->nama_syarikat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.nama_syarikat') }}</label>
                            <input id="nama_syarikat" type="text" name="nama_syarikat" value="{{ old('nama_syarikat', $logbook->nama_syarikat) }}"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.bulan') }} <span class="text-red-500">*</span></label>
                            <select name="bulan" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                @foreach(['Januari','Februari','Mac','April','Mei','Jun','Julai','Ogos','September','Oktober','November','Disember'] as $b)
                                    <option value="{{ $b }}" {{ old('bulan', $logbook->bulan) === $b ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.tahun') }} <span class="text-red-500">*</span></label>
                            <input type="number" name="tahun" value="{{ old('tahun', $logbook->tahun) }}" required min="2020" max="2030" step="1"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.log_status_logbook') }} <span class="text-red-500">*</span></label>
                            <select name="status_logbook" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                @foreach(['Dikemukakan','Dalam Semakan','Lewat','Belum Dikemukakan'] as $sl)
                                    <option value="{{ $sl }}" {{ old('status_logbook', $logbook->status_logbook) === $sl ? 'selected' : '' }}>{{ $sl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.log_status_semakan') }} <span class="text-red-500">*</span></label>
                            <select name="status_semakan" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                @foreach(['Lulus','Dalam Proses','Perlu Semakan Semula','Belum Disemak'] as $ss)
                                    <option value="{{ $ss }}" {{ old('status_semakan', $logbook->status_semakan) === $ss ? 'selected' : '' }}>{{ $ss }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.log_nama_mentor') }}</label>
                            <input type="text" name="nama_mentor" value="{{ old('nama_mentor', $logbook->nama_mentor) }}"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.log_tarikh_semakan') }}</label>
                            <input type="date" name="tarikh_semakan" value="{{ old('tarikh_semakan', $logbook->tarikh_semakan?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.log_komen_mentor') }}</label>
                            <textarea name="komen_mentor" rows="3"
                                      class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('komen_mentor', $logbook->komen_mentor) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <button type="submit"
                            class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors shadow-sm">
                        {{ __('protege.save_changes') }}
                    </button>
                    <a href="{{ route('admin.logbook.show', $logbook) }}"
                       class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                        {{ __('protege.cancel') }}
                    </a>
                </div>
            </form>
        </div>

        {{-- ═══ Tab 2: Logbook File ═══ --}}
        <div x-show="tab === 'file'" style="display:none">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    {{ __('protege.log_file') }}
                </h3>

                {{-- Current file --}}
                @if($logbook->link_file_logbook)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-green-800">{{ $logbook->file_name ?? basename($logbook->link_file_logbook) }}</p>
                                <p class="text-xs text-green-600 mt-0.5">{{ __('protege.ss_current_file') }} &middot; {{ $logbook->bulan }} {{ $logbook->tahun }}</p>
                                @if($logbook->tarikh_upload)
                                    <p class="text-xs text-green-500">{{ __('protege.log_tarikh_upload') }}: {{ $logbook->tarikh_upload->format('d/m/Y') }}</p>
                                @endif
                            </div>
                            @if(Str::startsWith($logbook->link_file_logbook, ['http://', 'https://']))
                                <a href="{{ $logbook->link_file_logbook }}" target="_blank"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white text-green-700 rounded-lg text-sm font-medium border border-green-300 hover:bg-green-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    {{ __('protege.view') }}
                                </a>
                            @else
                                <a href="{{ Storage::url($logbook->link_file_logbook) }}" target="_blank"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white text-green-700 rounded-lg text-sm font-medium border border-green-300 hover:bg-green-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ __('protege.view') }}
                                </a>
                                <a href="{{ Storage::url($logbook->link_file_logbook) }}" download
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 rounded-lg text-sm font-medium border border-gray-300 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    {{ __('protege.download') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-5 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 font-medium">{{ __('protege.log_no_file') }}</p>
                        <p class="text-gray-400 text-xs mt-1">{{ __('protege.log_no_file_hint') }}</p>
                    </div>
                @endif

                {{-- Upload form --}}
                @if($errors->has('logbook_file'))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-red-600">{{ $errors->first('logbook_file') }}</p>
                    </div>
                @endif

                <div class="border border-dashed border-gray-300 rounded-xl p-5 bg-gray-50/50">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">{{ $logbook->link_file_logbook ? __('protege.ss_replace_file') : __('protege.log_upload_file') }}</h4>
                    <form method="POST" action="{{ route('admin.logbook.upload', $logbook) }}" enctype="multipart/form-data"
                          x-data="{ fileOk: false }">
                        @csrf
                        <div class="flex items-end gap-3">
                            <div class="flex-1">
                                <input type="file" name="logbook_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required
                                       @change="fileOk = $event.target.files.length > 0 && $event.target.files[0].size <= 10485760; if ($event.target.files[0]?.size > 10485760) { alert('File too large. Max 10MB.'); $event.target.value=''; fileOk=false; }"
                                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#1E3A5F] file:text-white hover:file:bg-[#274670] file:cursor-pointer">
                                <p class="text-xs text-gray-400 mt-1.5">{{ __('protege.ss_upload_hint') }}</p>
                            </div>
                            <button type="submit" :disabled="!fileOk"
                                    class="px-5 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors flex-shrink-0 disabled:opacity-40 disabled:cursor-not-allowed inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                {{ __('protege.upload') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const graduan = document.getElementById('id_graduan');
    if (!graduan) return;
    const syncLinkedFields = () => {
        const selected = graduan.options[graduan.selectedIndex];
        if (!selected || !selected.value) return;
        const syarikatId = selected.dataset.syarikatId || '';
        const fullName = selected.dataset.fullName || '';
        const syarikatName = selected.dataset.syarikatName || '';
        const syarikat = document.getElementById('id_syarikat');
        const namaGraduan = document.getElementById('nama_graduan');
        const namaSyarikat = document.getElementById('nama_syarikat');
        if (syarikat && syarikatId) syarikat.value = syarikatId;
        if (namaGraduan && fullName) namaGraduan.value = fullName;
        if (namaSyarikat && syarikatName) namaSyarikat.value = syarikatName;
    };
    graduan.addEventListener('change', syncLinkedFields);
    syncLinkedFields();
});
</script>
@endpush
@endsection
