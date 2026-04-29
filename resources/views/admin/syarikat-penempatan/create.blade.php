@extends('layouts.admin')
@section('title', __('protege.spen_add_title'))
@section('page-title', __('protege.spen_add_title'))

@section('content')
<div class="max-w-3xl">
    <div class="mb-5">
        <a href="{{ route('admin.syarikat-penempatan.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            {{ __('protege.back_to_list') }}
        </a>
    </div>

    <form method="POST" action="{{ route('admin.syarikat-penempatan.store') }}">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('protege.spen_maklumat') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.spen_nama_syarikat') }} *</label>
                    <input type="text" name="nama_syarikat" value="{{ old('nama_syarikat') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('nama_syarikat') border-red-400 @enderror">
                    @error('nama_syarikat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.spen_sektor') }} *</label>
                    <input type="text" name="sektor_industri" value="{{ old('sektor_industri') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                           placeholder="{{ __('protege.placeholder_sektor') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.spen_kuota') }} *</label>
                    <input type="number" name="kuota_dipersetujui" value="{{ old('kuota_dipersetujui', 0) }}" required min="0" step="1" inputmode="numeric" data-numeric="integer"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.spen_graduan_count') }}</label>
                    <input type="number" name="jumlah_graduan_ditempatkan" value="{{ old('jumlah_graduan_ditempatkan', 0) }}" min="0" step="1" inputmode="numeric" data-numeric="integer"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.spen_laporan_bulanan') }} *</label>
                    <select name="laporan_bulanan" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        @foreach(['Lengkap' => __('protege.laporan_lengkap'), 'Tertangguh' => __('protege.laporan_tertangguh'), 'Tidak Lengkap' => __('protege.laporan_tidak_lengkap')] as $s => $label)
                            <option value="{{ $s }}" {{ old('laporan_bulanan', 'Lengkap') === $s ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.spen_status_pematuhan') }} *</label>
                    <select name="status_pematuhan" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        @foreach(['Cemerlang' => __('protege.pematuhan_cemerlang'), 'Baik' => __('protege.pematuhan_baik'), 'Memuaskan' => __('protege.pematuhan_memuaskan'), 'Perlu Penambahbaikan' => __('protege.pematuhan_perlu')] as $s => $label)
                            <option value="{{ $s }}" {{ old('status_pematuhan', 'Baik') === $s ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2 border-t border-gray-100 pt-5">
                    <h4 class="font-medium text-gray-700 mb-4">{{ __('protege.spen_pic_section') }}</h4>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.spen_nama_pic') }} *</label>
                    <input type="text" name="pic" value="{{ old('pic') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.spen_no_telefon_pic') }} *</label>
                    <input type="text" name="no_telefon_pic" value="{{ old('no_telefon_pic') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.spen_email_pic') }} *</label>
                    <input type="email" name="email_pic" value="{{ old('email_pic') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.catatan') }}</label>
                    <textarea name="catatan" rows="3"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-5 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors shadow-sm">{{ __('protege.save') }}</button>
            <a href="{{ route('admin.syarikat-penempatan.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">{{ __('protege.cancel') }}</a>
        </div>
    </form>
</div>
@endsection