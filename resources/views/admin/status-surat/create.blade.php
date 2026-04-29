@extends('layouts.admin')

@section('title', __('protege.ss_add'))
@section('page-title', __('protege.ss_add'))

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.status-surat.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('protege.back_to_list') }}
        </a>
    </div>

    <form method="POST" action="{{ route('admin.status-surat.store') }}">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('protege.ss_title') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Syarikat Pelaksana --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.pelaksana') }} <span class="text-red-500">*</span></label>
                    <select name="id_pelaksana" id="id_pelaksana" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('id_pelaksana') border-red-400 @enderror">
                        <option value="">-- {{ __('protege.all_pelaksana') }} --</option>
                        @foreach($pelaksana as $p)
                            <option value="{{ $p->id_pelaksana }}" {{ old('id_pelaksana') == $p->id_pelaksana ? 'selected' : '' }}>{{ $p->nama_syarikat }}</option>
                        @endforeach
                    </select>
                    @error('id_pelaksana')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Jenis Surat --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.ss_jenis_surat') }} <span class="text-red-500">*</span></label>
                    <select name="jenis_surat" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">-- {{ __('protege.all') }} --</option>
                        @foreach(['Surat Kuning', 'Surat Biru'] as $js)
                            <option value="{{ $js }}" {{ old('jenis_surat') === $js ? 'selected' : '' }}>{{ $js }}</option>
                        @endforeach
                    </select>
                    @error('jenis_surat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Graduan Selection --}}
                <div class="md:col-span-2" x-data="talentSelector()">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.nama_graduan') }}</label>
                    <div class="relative">
                        <input type="text" x-model="searchQuery" @input="filterTalents()" @focus="openDropdown()"
                               placeholder="{{ __('protege.kh_search') }}..."
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">

                        {{-- Selected talent display --}}
                        <div x-show="selectedId" class="mt-2 inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg text-sm">
                            <span class="font-mono font-bold" x-text="selectedId"></span>
                            <span x-text="selectedName"></span>
                            <button type="button" @click="clearSelection()" class="ml-1 text-blue-400 hover:text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Dropdown list --}}
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

                {{-- Status Surat --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.ss_status_surat') }} <span class="text-red-500">*</span></label>
                    <select name="status_surat" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">-- {{ __('protege.all') }} --</option>
                        @foreach(['Belum Mula', 'Draft', 'Semakan', 'Tandatangan', 'Hantar', 'Selesai'] as $ss)
                            <option value="{{ $ss }}" {{ old('status_surat') === $ss ? 'selected' : '' }}>{{ $ss }}</option>
                        @endforeach
                    </select>
                    @error('status_surat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- PIC --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.ss_pic') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="pic_responsible" value="{{ old('pic_responsible') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    @error('pic_responsible')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
                        <input type="date" name="{{ $field }}" value="{{ old($field) }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 gap-5 mt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.ss_isu_halangan') }}</label>
                    <textarea name="isu_halangan" rows="2"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('isu_halangan') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.catatan') }}</label>
                    <textarea name="catatan" rows="2"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-5 flex items-center gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors shadow-sm">
                {{ __('protege.save') }}
            </button>
            <a href="{{ route('admin.status-surat.index') }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                {{ __('protege.cancel') }}
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function talentSelector() {
    const allTalents = @json($talentsJson);

    return {
        searchQuery: '',
        showDropdown: false,
        selectedId: '{{ old("id_graduan", "") }}',
        selectedName: '{{ old("nama_graduan", "") }}',
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
