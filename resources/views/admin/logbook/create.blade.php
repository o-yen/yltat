@extends('layouts.admin')

@section('title', __('protege.log_add_record'))
@section('page-title', __('protege.log_add_record'))

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.logbook.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('protege.back_to_list') }}
        </a>
    </div>

    <form method="POST" action="{{ route('admin.logbook.store') }}">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('protege.log_info') }}</h3>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.nama_graduan') }} <span class="text-red-500">*</span></label>
                    <input id="nama_graduan" type="text" name="nama_graduan" value="{{ old('nama_graduan') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('nama_graduan') border-red-400 @enderror">
                    @error('nama_graduan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.nama_syarikat') }}</label>
                    <input id="nama_syarikat" type="text" name="nama_syarikat" value="{{ old('nama_syarikat') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.bulan') }} <span class="text-red-500">*</span></label>
                    <select name="bulan" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('protege.select_month') }}</option>
                        @foreach(['Januari' => __('common.month_names.january'), 'Februari' => __('common.month_names.february'), 'Mac' => __('common.month_names.march'), 'April' => __('common.month_names.april'), 'Mei' => __('common.month_names.may'), 'Jun' => __('common.month_names.june'), 'Julai' => __('common.month_names.july'), 'Ogos' => __('common.month_names.august'), 'September' => __('common.month_names.september'), 'Oktober' => __('common.month_names.october'), 'November' => __('common.month_names.november'), 'Disember' => __('common.month_names.december')] as $value => $label)
                            <option value="{{ $value }}" {{ old('bulan') === $value ? 'selected' : '' }}>{{ $label }}</option>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.log_status_logbook') }} <span class="text-red-500">*</span></label>
                    <select name="status_logbook" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('protege.select_status') }}</option>
                        @foreach(['Dikemukakan' => __('protege.lb_dikemukakan'), 'Dalam Semakan' => __('protege.lb_dalam_semakan'), 'Lewat' => __('protege.lb_lewat'), 'Belum Dikemukakan' => __('protege.lb_belum')] as $value => $label)
                            <option value="{{ $value }}" {{ old('status_logbook') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status_logbook')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.log_tarikh_upload') }}</label>
                    <input type="date" name="tarikh_upload" value="{{ old('tarikh_upload') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.log_link_file') }}</label>
                    <input type="text" name="link_file_logbook" value="{{ old('link_file_logbook') }}" placeholder="https://..."
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.log_status_semakan') }} <span class="text-red-500">*</span></label>
                    <select name="status_semakan" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('protege.select_status') }}</option>
                        @foreach(['Lulus' => __('protege.semakan_lulus'), 'Dalam Proses' => __('protege.semakan_dalam_proses'), 'Perlu Semakan Semula' => __('protege.semakan_perlu_semula'), 'Belum Disemak' => __('protege.semakan_belum')] as $value => $label)
                            <option value="{{ $value }}" {{ old('status_semakan') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status_semakan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.log_nama_mentor') }}</label>
                    <input type="text" name="nama_mentor" value="{{ old('nama_mentor') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.log_tarikh_semakan') }}</label>
                    <input type="date" name="tarikh_semakan" value="{{ old('tarikh_semakan') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.log_komen_mentor') }}</label>
                    <textarea name="komen_mentor" rows="3"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('komen_mentor') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-5 flex items-center gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors shadow-sm">
                {{ __('protege.save') }}
            </button>
            <a href="{{ route('admin.logbook.index') }}"
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
