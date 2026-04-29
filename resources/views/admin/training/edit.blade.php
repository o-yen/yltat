@extends('layouts.admin')

@section('title', __('protege.trn_edit_record') . ' - ' . $training->tajuk_training)
@section('page-title', __('protege.trn_edit_record'))

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.training.show', $training) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('protege.back_to_details') }}
        </a>
    </div>

    <div class="mb-4 inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg text-sm">
        <span class="font-mono font-bold">{{ $training->id_training ?? $training->id }}</span>
        <span class="text-blue-400">|</span>
        <span>{{ $training->tajuk_training }}</span>
    </div>

    <form method="POST" action="{{ route('admin.training.update', $training) }}">
        @csrf
        @method('PUT')

        <!-- Maklumat Asas -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('protege.trn_basic_info') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.penempatan') }} <span class="text-red-500">*</span></label>
                    <select name="id_syarikat" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('id_syarikat') border-red-400 @enderror">
                        <option value="">{{ __('protege.select_company') }}</option>
                        @foreach($penempatan as $s)
                            <option value="{{ $s->id_syarikat }}" {{ old('id_syarikat', $training->id_syarikat) == $s->id_syarikat ? 'selected' : '' }}>{{ $s->nama_syarikat }}</option>
                        @endforeach
                    </select>
                    @error('id_syarikat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_jenis') }} <span class="text-red-500">*</span></label>
                    <select name="jenis_training" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('jenis_training') border-red-400 @enderror">
                        <option value="">{{ __('protege.select_type') }}</option>
                        @foreach(['Soft Skills' => __('protege.trn_soft_skills'), 'Technical' => __('protege.trn_technical'), 'Safety' => __('protege.trn_safety'), 'Other' => __('protege.trn_other')] as $value => $label)
                            <option value="{{ $value }}" {{ old('jenis_training', $training->jenis_training) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('jenis_training')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_tajuk') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="tajuk_training" value="{{ old('tajuk_training', $training->tajuk_training) }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('tajuk_training') border-red-400 @enderror">
                    @error('tajuk_training')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_sesi') }} <span class="text-red-500">*</span></label>
                    <select name="sesi" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('sesi') border-red-400 @enderror">
                        <option value="">{{ __('protege.select_session') }}</option>
                        <option value="Session 1" {{ old('sesi', $training->sesi) === 'Session 1' ? 'selected' : '' }}>Session 1</option>
                        <option value="Session 2" {{ old('sesi', $training->sesi) === 'Session 2' ? 'selected' : '' }}>Session 2</option>
                    </select>
                    @error('sesi')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_tarikh') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="tarikh_training" value="{{ old('tarikh_training', $training->tarikh_training?->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('tarikh_training') border-red-400 @enderror">
                    @error('tarikh_training')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_durasi') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="durasi_jam" value="{{ old('durasi_jam', $training->durasi_jam) }}" required min="1" step="1" inputmode="numeric" data-numeric="integer"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('durasi_jam') border-red-400 @enderror">
                    @error('durasi_jam')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_lokasi') }}</label>
                    <input type="text" name="lokasi" value="{{ old('lokasi', $training->lokasi) }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_trainer') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="trainer_name" value="{{ old('trainer_name', $training->trainer_name) }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('trainer_name') border-red-400 @enderror">
                    @error('trainer_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_trainer_type') }} <span class="text-red-500">*</span></label>
                    <select name="trainer_type" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('trainer_type') border-red-400 @enderror">
                        <option value="">{{ __('protege.select_type') }}</option>
                        <option value="Internal" {{ old('trainer_type', $training->trainer_type) === 'Internal' ? 'selected' : '' }}>{{ __('protege.trn_internal') }}</option>
                        <option value="External" {{ old('trainer_type', $training->trainer_type) === 'External' ? 'selected' : '' }}>{{ __('protege.trn_external') }}</option>
                    </select>
                    @error('trainer_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.status') }} <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('status') border-red-400 @enderror">
                        <option value="">{{ __('protege.select_status') }}</option>
                        @foreach(['Dirancang' => __('protege.trn_dirancang'), 'Dalam Proses' => __('protege.trn_dalam_proses'), 'Selesai' => __('protege.trn_selesai'), 'Dibatalkan' => __('protege.trn_dibatalkan')] as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $training->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Kehadiran & Penilaian -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('protege.trn_attendance_assessment') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_dijemput') }}</label>
                    <input type="number" name="jumlah_dijemput" value="{{ old('jumlah_dijemput', $training->jumlah_dijemput) }}" min="0" step="1" inputmode="numeric" data-numeric="integer"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_hadir') }}</label>
                    <input type="number" name="jumlah_hadir" value="{{ old('jumlah_hadir', $training->jumlah_hadir) }}" min="0" step="1" inputmode="numeric" data-numeric="integer"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_topik') }}</label>
                    <textarea name="topik_covered" rows="3"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('topik_covered', $training->topik_covered) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_pre_assessment') }}</label>
                    <input type="number" name="pre_assessment_avg" value="{{ old('pre_assessment_avg', $training->pre_assessment_avg) }}" min="0" max="10" step="0.1" inputmode="decimal"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_post_assessment') }}</label>
                    <input type="number" name="post_assessment_avg" value="{{ old('post_assessment_avg', $training->post_assessment_avg) }}" min="0" max="10" step="0.1" inputmode="decimal"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_kepuasan') }} (0-10)</label>
                    <input type="number" name="skor_kepuasan" value="{{ old('skor_kepuasan', $training->skor_kepuasan) }}" min="0" max="10" step="0.1" inputmode="decimal"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
            </div>
        </div>

        <!-- Bajet -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
            <h3 class="font-semibold text-gray-800 mb-5">{{ __('protege.trn_budget_info') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_budget_alloc') }}</label>
                    <input type="number" name="budget_allocated" value="{{ old('budget_allocated', $training->budget_allocated) }}" min="0" step="0.01" inputmode="decimal"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.trn_budget_spent') }}</label>
                    <input type="number" name="budget_spent" value="{{ old('budget_spent', $training->budget_spent) }}" min="0" step="0.01" inputmode="decimal"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('protege.catatan') }}</label>
                    <textarea name="catatan" rows="3"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('catatan', $training->catatan) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors shadow-sm">
                {{ __('protege.save_changes') }}
            </button>
            <a href="{{ route('admin.training.show', $training) }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                {{ __('protege.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
