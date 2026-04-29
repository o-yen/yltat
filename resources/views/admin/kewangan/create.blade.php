@extends('layouts.admin')

@section('title', __('protege.kw_add'))
@section('page-title', __('protege.kw_add'))

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.kewangan.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('protege.back_to_list') }}
        </a>
    </div>

    <form method="POST" action="{{ route('admin.kewangan.store') }}">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('protege.kw_info') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div x-data="graduanSelector()" @click.away="showDropdown = false">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.nama_graduan') }} <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" x-model="searchQuery" @input="filterList()" @focus="filterList(); showDropdown = true"
                               placeholder="{{ __('messages.search') }}..."
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('id_graduan') border-red-400 @enderror">
                        <div x-show="selectedId" class="mt-1.5 inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg text-xs">
                            <span class="font-mono font-bold" x-text="selectedId"></span>
                            <span x-text="selectedName"></span>
                            <button type="button" @click="clearSelection()" class="text-blue-400 hover:text-blue-600 flex-shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div x-show="showDropdown && filtered.length > 0"
                             class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-52 overflow-y-auto">
                            <template x-for="item in filtered" :key="item.id">
                                <button type="button" @click="selectItem(item)"
                                        class="w-full text-left px-3 py-2.5 text-sm hover:bg-blue-50 border-b border-gray-50 flex items-center gap-2">
                                    <span class="font-mono text-xs text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded" x-text="item.id"></span>
                                    <span class="text-gray-800" x-text="item.name"></span>
                                    <span class="text-xs text-gray-400 ml-auto" x-text="item.pelaksana"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <input type="hidden" name="id_graduan" :value="selectedId">
                    @error('id_graduan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.pelaksana') }}</label>
                    <select id="id_pelaksana" name="id_pelaksana" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('protege.select_pelaksana') }}</option>
                        @foreach($pelaksana as $p)
                            <option value="{{ $p->id_pelaksana }}" {{ old('id_pelaksana') == $p->id_pelaksana ? 'selected' : '' }}>{{ $p->nama_syarikat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.bulan') }} <span class="text-red-500">*</span></label>
                    <select name="bulan" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('protege.select_month') }}</option>
                        @foreach(['Januari','Februari','Mac','April','Mei','Jun','Julai','Ogos','September','Oktober','November','Disember'] as $b)
                            <option value="{{ $b }}" {{ old('bulan') === $b ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>
                    @error('bulan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.tahun') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" required min="2020" max="2030" step="1" inputmode="numeric" data-numeric="integer"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.kw_tarikh_mula') }}</label>
                    <input type="date" name="tarikh_mula_kerja" value="{{ old('tarikh_mula_kerja') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.kw_tarikh_akhir') }}</label>
                    <input type="date" name="tarikh_akhir_kerja" value="{{ old('tarikh_akhir_kerja') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.kw_hari_bekerja') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="hari_bekerja_sebenar" value="{{ old('hari_bekerja_sebenar') }}" required min="0" step="1" inputmode="numeric" data-numeric="integer"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    @error('hari_bekerja_sebenar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.kw_hari_dalam_bulan') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="hari_dalam_bulan" value="{{ old('hari_dalam_bulan') }}" required min="1" step="1" inputmode="numeric" data-numeric="integer"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    @error('hari_dalam_bulan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.kw_elaun_penuh') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="elaun_penuh" value="{{ old('elaun_penuh') }}" required min="0" step="0.01" inputmode="decimal"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    @error('elaun_penuh')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.kw_status_bayaran') }} <span class="text-red-500">*</span></label>
                    <select name="status_bayaran" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('protege.select_status') }}</option>
                        @foreach(['Selesai' => __('protege.bayaran_selesai'), 'Dalam Proses' => __('protege.bayaran_dalam_proses'), 'Lewat' => __('protege.bayaran_lewat')] as $val => $label)
                            <option value="{{ $val }}" {{ old('status_bayaran') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status_bayaran')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.kw_tarikh_bayar') }}</label>
                    <input type="date" name="tarikh_bayar" value="{{ old('tarikh_bayar') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.kw_tarikh_jangka') }}</label>
                    <input type="date" name="tarikh_jangka_bayar" value="{{ old('tarikh_jangka_bayar') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.catatan') }}</label>
                    <textarea name="catatan" rows="3"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-5 flex items-center gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors shadow-sm">
                {{ __('protege.save_record') }}
            </button>
            <a href="{{ route('admin.kewangan.index') }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                {{ __('protege.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection


@php
    $talentsJson = $talents->map(function($t) {
        return ['id' => $t->id_graduan, 'name' => $t->full_name, 'pelaksana' => $t->id_pelaksana ?? '', 'pelaksanaId' => $t->id_pelaksana ?? ''];
    })->values();
@endphp
@push('scripts')
<script>
function graduanSelector() {
    const allTalents = @json($talentsJson);

    const editId = '{{ old("id_graduan", "") }}';
    const found = allTalents.find(t => t.id === editId);

    return {
        searchQuery: found ? found.name : '',
        showDropdown: false,
        selectedId: editId,
        selectedName: found ? found.name : '',
        filtered: [],

        filterList() {
            const q = (this.searchQuery || '').toLowerCase();
            this.filtered = allTalents.filter(t =>
                !q || t.name.toLowerCase().includes(q) || t.id.toLowerCase().includes(q)
            ).slice(0, 30);
            this.showDropdown = true;
        },

        selectItem(item) {
            this.selectedId = item.id;
            this.selectedName = item.name;
            this.searchQuery = item.name;
            this.showDropdown = false;

            // Auto-fill pelaksana
            const pelaksana = document.getElementById('id_pelaksana');
            if (pelaksana && item.pelaksanaId) {
                pelaksana.value = item.pelaksanaId;
            }
        },

        clearSelection() {
            this.selectedId = '';
            this.selectedName = '';
            this.searchQuery = '';
        }
    };
}
</script>
@endpush
