@extends('layouts.admin')

@section('title', __('protege.isu_add_title'))
@section('page-title', __('protege.isu_add_title'))

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.isu-risiko.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('protege.back_to_list') }}
        </a>
    </div>

    <form method="POST" action="{{ route('admin.isu-risiko.store') }}">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('protege.isu_maklumat') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.isu_tarikh') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="tarikh_isu" value="{{ old('tarikh_isu', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('tarikh_isu') border-red-400 @enderror">
                    @error('tarikh_isu')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.isu_kategori') }} <span class="text-red-500">*</span></label>
                    <select name="kategori_isu" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('kategori_isu') border-red-400 @enderror">
                        <option value="">{{ __('protege.select_kategori') }}</option>
                        @foreach(['Bayaran Lewat' => __('protege.kat_bayaran_lewat'), 'Kehadiran Rendah' => __('protege.kat_kehadiran_rendah'), 'Prestasi Lemah' => __('protege.kat_prestasi_lemah'), 'Logbook Lewat' => __('protege.kat_logbook_lewat'), 'Isu Pematuhan' => __('protege.kat_pematuhan'), 'Masalah Komunikasi' => __('protege.kat_komunikasi'), 'Lain-lain' => __('protege.kat_lain')] as $ki => $label)
                            <option value="{{ $ki }}" {{ old('kategori_isu') === $ki ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('kategori_isu')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.pelaksana') }}</label>
                    <select name="id_pelaksana" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('protege.select_pelaksana') }}</option>
                        @foreach($pelaksana as $p)
                            <option value="{{ $p->id_pelaksana }}" {{ old('id_pelaksana') == $p->id_pelaksana ? 'selected' : '' }}>{{ $p->nama_syarikat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.penempatan') }}</label>
                    <select name="id_syarikat" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('protege.select_penempatan') }}</option>
                        @foreach($penempatan as $s)
                            <option value="{{ $s->id_syarikat }}" {{ old('id_syarikat') == $s->id_syarikat ? 'selected' : '' }}>{{ $s->nama_syarikat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.isu_tahap_risiko') }} <span class="text-red-500">*</span></label>
                    <select name="tahap_risiko" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('protege.select_tahap') }}</option>
                        @foreach(['Kritikal' => __('protege.risiko_kritikal'), 'Tinggi' => __('protege.risiko_tinggi'), 'Sederhana' => __('protege.risiko_sederhana'), 'Rendah' => __('protege.risiko_rendah')] as $tr => $label)
                            <option value="{{ $tr }}" {{ old('tahap_risiko') === $tr ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('tahap_risiko')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.status') }} <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('protege.select_status') }}</option>
                        @foreach(['Baru' => __('protege.isu_status_baru'), 'Dalam Tindakan' => __('protege.isu_status_dalam'), 'Selesai' => __('protege.isu_status_selesai'), 'Ditutup' => __('protege.isu_status_ditutup')] as $st => $label)
                            <option value="{{ $st }}" {{ old('status', 'Baru') === $st ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.isu_pic') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="pic" value="{{ old('pic') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    @error('pic')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.isu_tarikh_tindakan') }}</label>
                    <input type="date" name="tarikh_tindakan" value="{{ old('tarikh_tindakan') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.isu_tarikh_tutup') }}</label>
                    <input type="date" name="tarikh_tutup" value="{{ old('tarikh_tutup') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.isu_butiran') }} <span class="text-red-500">*</span></label>
                    <textarea name="butiran_isu" rows="4" required
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('butiran_isu') border-red-400 @enderror">{{ old('butiran_isu') }}</textarea>
                    @error('butiran_isu')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.isu_tindakan') }}</label>
                    <textarea name="tindakan_diambil" rows="3"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('tindakan_diambil') }}</textarea>
                </div>

                <div class="md:col-span-2">
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
            <a href="{{ route('admin.isu-risiko.index') }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                {{ __('protege.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection