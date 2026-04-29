@extends('layouts.admin')

@section('title', __('protege.kh_add'))
@section('page-title', __('protege.kh_add'))

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.kehadiran.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('protege.back_to_list') }}
        </a>
    </div>

    <form method="POST" action="{{ route('admin.kehadiran.store') }}">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('protege.kh_info') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.nama_graduan') }} <span class="text-red-500">*</span></label>
                    <select id="id_graduan" name="id_graduan" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('id_graduan') border-red-400 @enderror">
                        <option value="">-- {{ __('protege.select_graduan') }} --</option>
                        @foreach($talents as $t)
                            <option value="{{ $t->id_graduan }}" data-full-name="{{ $t->full_name }}" data-pelaksana-id="{{ $t->id_pelaksana }}" data-syarikat-id="{{ $t->id_syarikat_penempatan }}" data-syarikat-name="{{ $t->syarikatPenempatan?->nama_syarikat }}" {{ old('id_graduan') == $t->id_graduan ? 'selected' : '' }}>
                                {{ $t->full_name }} ({{ $t->id_graduan }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_graduan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.penempatan') }}</label>
                    <select id="id_syarikat" name="id_syarikat" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('protege.select_company') }}</option>
                        @foreach($penempatan as $s)
                            <option value="{{ $s->id_syarikat }}" {{ old('id_syarikat') == $s->id_syarikat ? 'selected' : '' }}>{{ $s->nama_syarikat }}</option>
                        @endforeach
                    </select>
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
                    @error('tahun')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.kh_hari_hadir') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="hari_hadir" value="{{ old('hari_hadir') }}" required min="0" step="1" inputmode="numeric" data-numeric="integer"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    @error('hari_hadir')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.kh_hari_bekerja') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="hari_bekerja" value="{{ old('hari_bekerja') }}" required min="1" step="1" inputmode="numeric" data-numeric="integer"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    @error('hari_bekerja')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.skor_prestasi_range') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="skor_prestasi" value="{{ old('skor_prestasi') }}" required min="1" max="10" step="1" inputmode="numeric" data-numeric="integer"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    @error('skor_prestasi')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.kh_status_logbook') }} <span class="text-red-500">*</span></label>
                    <select name="status_logbook" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('protege.select_status') }}</option>
                        @foreach(['Dikemukakan' => __('protege.lb_dikemukakan'), 'Lewat' => __('protege.lb_lewat'), 'Belum Dikemukakan' => __('protege.lb_belum')] as $val => $label)
                            <option value="{{ $val }}" {{ old('status_logbook') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status_logbook')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.kh_komen_mentor') }}</label>
                    <textarea name="komen_mentor" rows="3"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('komen_mentor') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-5 flex items-center gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors shadow-sm">
                {{ __('protege.save_record') }}
            </button>
            <a href="{{ route('admin.kehadiran.index') }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                {{ __('protege.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const graduan = document.getElementById('id_graduan');
    if (!graduan) return;

    const syncLinkedFields = () => {
        const selected = graduan.options[graduan.selectedIndex];
        if (!selected || !selected.value) return;

        const pelaksanaId = selected.dataset.pelaksanaId || '';
        const syarikatId = selected.dataset.syarikatId || '';
        const fullName = selected.dataset.fullName || '';
        const syarikatName = selected.dataset.syarikatName || '';

        const pelaksana = document.getElementById('id_pelaksana');
        const syarikat = document.getElementById('id_syarikat');
        const namaGraduan = document.getElementById('nama_graduan');
        const namaSyarikat = document.getElementById('nama_syarikat');

        if (pelaksana && pelaksanaId) pelaksana.value = pelaksanaId;
        if (syarikat && syarikatId) syarikat.value = syarikatId;
        if (namaGraduan && fullName) namaGraduan.value = fullName;
        if (namaSyarikat && syarikatName) namaSyarikat.value = syarikatName;
    };

    graduan.addEventListener('change', syncLinkedFields);
    syncLinkedFields();
});
</script>
@endpush
