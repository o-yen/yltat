@extends('layouts.admin')

@section('title', __('protege.edit') . ' ' . __('protege.ss_title'))
@section('page-title', __('protege.edit') . ' ' . __('protege.ss_title'))

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.status-surat.show', $statusSurat) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('protege.back_to_profile') }}
        </a>
    </div>

    <div class="mb-4 inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg text-sm">
        <span class="font-mono font-bold">{{ $statusSurat->jenis_surat }}</span>
        <span class="text-blue-400">|</span>
        <span>{{ $statusSurat->nama_graduan ?? $statusSurat->id_graduan ?? '-' }}</span>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: '{{ $errors->has('file_attachment') ? 'attachment' : 'details' }}' }">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-5 overflow-hidden">
            <div class="flex border-b border-gray-100">
                <button @click="tab = 'details'"
                        :class="tab === 'details' ? 'border-[#1E3A5F] text-[#1E3A5F] font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-5 py-3 text-sm border-b-2 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    {{ __('protege.ss_title') }}
                </button>
                <button @click="tab = 'attachment'"
                        :class="tab === 'attachment' ? 'border-[#1E3A5F] text-[#1E3A5F] font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-5 py-3 text-sm border-b-2 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    {{ __('protege.ss_attachment') }}
                    @if($statusSurat->file_attachment)
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    @endif
                </button>
            </div>
        </div>

        {{-- ═══ Tab 1: Letter Details ═══ --}}
        <div x-show="tab === 'details'">
            <form method="POST" action="{{ route('admin.status-surat.update', $statusSurat) }}">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-5">{{ __('protege.ss_title') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.pelaksana') }} <span class="text-red-500">*</span></label>
                            <select name="id_pelaksana" id="id_pelaksana" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                <option value="">-- {{ __('protege.all_pelaksana') }} --</option>
                                @foreach($pelaksana as $p)
                                    <option value="{{ $p->id_pelaksana }}" {{ old('id_pelaksana', $statusSurat->id_pelaksana) == $p->id_pelaksana ? 'selected' : '' }}>{{ $p->nama_syarikat }}</option>
                                @endforeach
                            </select>
                            @error('id_pelaksana')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.ss_jenis_surat') }} <span class="text-red-500">*</span></label>
                            <select name="jenis_surat" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                @foreach(['Surat Kuning', 'Surat Biru'] as $js)
                                    <option value="{{ $js }}" {{ old('jenis_surat', $statusSurat->jenis_surat) === $js ? 'selected' : '' }}>{{ $js }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2" x-data="talentSelector()">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.nama_graduan') }}</label>
                            <div class="relative">
                                <input type="text" x-model="searchQuery" @input="filterTalents()" @focus="openDropdown()"
                                       placeholder="{{ __('protege.kh_search') }}..."
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                <div x-show="selectedId" class="mt-2 inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg text-sm">
                                    <span class="font-mono font-bold" x-text="selectedId"></span>
                                    <span x-text="selectedName"></span>
                                    <button type="button" @click="clearSelection()" class="ml-1 text-blue-400 hover:text-blue-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div x-show="showDropdown && filteredTalents.length > 0" @click.away="showDropdown = false"
                                     class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <template x-for="talent in filteredTalents" :key="talent.id">
                                        <button type="button" @click="selectTalent(talent)"
                                                class="w-full text-left px-4 py-2.5 text-sm hover:bg-blue-50 border-b border-gray-50 flex items-center gap-3">
                                            <span class="font-mono text-xs text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded" x-text="talent.id"></span>
                                            <span class="text-gray-800" x-text="talent.name"></span>
                                            <span class="text-xs text-gray-400 ml-auto" x-text="talent.pelaksana"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <input type="hidden" name="id_graduan" :value="selectedId">
                            <input type="hidden" name="nama_graduan" :value="selectedName">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.ss_status_surat') }} <span class="text-red-500">*</span></label>
                            <select name="status_surat" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                                @foreach(['Belum Mula', 'Draft', 'Semakan', 'Tandatangan', 'Hantar', 'Selesai'] as $ss)
                                    <option value="{{ $ss }}" {{ old('status_surat', $statusSurat->status_surat) === $ss ? 'selected' : '' }}>{{ $ss }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.ss_pic') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="pic_responsible" value="{{ old('pic_responsible', $statusSurat->pic_responsible) }}" required
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        </div>
                    </div>

                    <h3 class="font-semibold text-gray-800 mt-6 mb-5">{{ __('protege.tarikh') }} {{ __('protege.status') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        @foreach([
                            ['tarikh_mula_proses', __('protege.ss_tarikh_mula')],
                            ['tarikh_draft', __('protege.ss_tarikh_draft')],
                            ['tarikh_semakan', __('protege.ss_tarikh_semakan')],
                            ['tarikh_tandatangan', __('protege.ss_tarikh_tandatangan')],
                            ['tarikh_hantar', __('protege.ss_tarikh_hantar')],
                            ['tarikh_siap', __('protege.ss_tarikh_siap')],
                        ] as [$field, $label])
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $label }}</label>
                                <input type="date" name="{{ $field }}" value="{{ old($field, $statusSurat->$field?->format('Y-m-d')) }}"
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                            </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 gap-5 mt-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.ss_isu_halangan') }}</label>
                            <textarea name="isu_halangan" rows="2"
                                      class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('isu_halangan', $statusSurat->isu_halangan) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.catatan') }}</label>
                            <textarea name="catatan" rows="2"
                                      class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('catatan', $statusSurat->catatan) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <button type="submit"
                            class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors shadow-sm">
                        {{ __('protege.save_changes') }}
                    </button>
                    <a href="{{ route('admin.status-surat.show', $statusSurat) }}"
                       class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                        {{ __('protege.cancel') }}
                    </a>
                </div>
            </form>
        </div>

        {{-- ═══ Tab 2: Attachment / Letter Copy ═══ --}}
        <div x-show="tab === 'attachment'" style="display:none">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    {{ __('protege.ss_letter_copy') }}
                </h3>

                {{-- Current file --}}
                @if($statusSurat->file_attachment)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-green-800">{{ $statusSurat->file_name ?? basename($statusSurat->file_attachment) }}</p>
                                <p class="text-xs text-green-600 mt-0.5">{{ __('protege.ss_current_file') }} &middot; {{ $statusSurat->jenis_surat }}</p>
                            </div>
                            <a href="{{ Storage::url($statusSurat->file_attachment) }}" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-white text-green-700 rounded-lg text-sm font-medium border border-green-300 hover:bg-green-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                {{ __('protege.view') }}
                            </a>
                            <a href="{{ Storage::url($statusSurat->file_attachment) }}" download
                               class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 rounded-lg text-sm font-medium border border-gray-300 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                {{ __('protege.download') }}
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-5 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 font-medium">{{ __('protege.ss_no_file') }}</p>
                        <p class="text-gray-400 text-xs mt-1">{{ __('protege.ss_no_file_hint') }}</p>
                    </div>
                @endif

                {{-- Upload form (separate from main edit form) --}}
                @if($errors->has('file_attachment'))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-red-600">{{ $errors->first('file_attachment') }}</p>
                    </div>
                @endif

                <div class="border border-dashed border-gray-300 rounded-xl p-5 bg-gray-50/50">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">{{ $statusSurat->file_attachment ? __('protege.ss_replace_file') : __('protege.ss_upload_letter') }}</h4>
                    <form method="POST" action="{{ route('admin.status-surat.upload', $statusSurat) }}" enctype="multipart/form-data"
                          x-data="{ fileOk: false }">
                        @csrf
                        <div class="flex items-end gap-3">
                            <div class="flex-1">
                                <input type="file" name="file_attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required
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
function talentSelector() {
    const allTalents = @json($talentsJson);

    return {
        searchQuery: '',
        showDropdown: false,
        selectedId: '{{ old("id_graduan", $statusSurat->id_graduan ?? "") }}',
        selectedName: '{{ old("nama_graduan", $statusSurat->nama_graduan ?? "") }}',
        filteredTalents: [],

        init() {
            this.searchQuery = this.selectedName;
        },

        filterTalents() {
            const q = (this.searchQuery || '').toLowerCase();
            const pelaksana = document.getElementById('id_pelaksana')?.value || '';
            this.filteredTalents = allTalents.filter(t => {
                const id = (t.id || '').toLowerCase();
                const name = (t.name || '').toLowerCase();
                const pel = t.pelaksana || '';
                const matchSearch = !q || name.includes(q) || id.includes(q);
                const matchPelaksana = !pelaksana || pel === pelaksana;
                return matchSearch && matchPelaksana;
            }).slice(0, 30);
            this.showDropdown = this.filteredTalents.length > 0;
        },

        openDropdown() {
            this.filterTalents();
            this.showDropdown = true;
        },

        selectTalent(talent) {
            this.selectedId = talent.id;
            this.selectedName = talent.name;
            this.searchQuery = talent.name;
            this.showDropdown = false;
        },

        clearSelection() {
            this.selectedId = '';
            this.selectedName = '';
            this.searchQuery = '';
            this.filteredTalents = [];
        }
    };
}
</script>
@endpush
@endsection
