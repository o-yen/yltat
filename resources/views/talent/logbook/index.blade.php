@extends('layouts.talent')

@section('title', __('talent.logbook_title'))
@section('page-title', __('talent.logbook_title'))

@section('content')
<div class="space-y-6">

    {{-- Submit Logbook --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] px-5 py-4">
            <h3 class="text-white font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                {{ __('talent.submit_logbook') }}
            </h3>
            <p class="text-blue-200 text-xs mt-0.5">{{ __('talent.submit_logbook_hint') }}</p>
        </div>
        <div class="p-5">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                    @foreach($errors->all() as $error)
                        <p class="text-sm text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('talent.logbook.upload') }}" enctype="multipart/form-data" x-data="{ fileOk: false }">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('talent.logbook_month') }} *</label>
                        <select name="bulan" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                            <option value="">-- {{ __('messages.select') }} --</option>
                            @foreach(['Januari','Februari','Mac','April','Mei','Jun','Julai','Ogos','September','Oktober','November','Disember'] as $b)
                                <option value="{{ $b }}" {{ old('bulan') === $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('talent.logbook_year') }} *</label>
                        <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" required min="2020" max="2030"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('talent.logbook_file') }} *</label>
                        <input type="file" name="logbook_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required
                               @change="fileOk = $event.target.files.length > 0 && $event.target.files[0].size <= 10485760; if ($event.target.files[0]?.size > 10485760) { alert('Max 10MB'); $event.target.value=''; fileOk=false; }"
                               class="w-full text-sm text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#1E3A5F] file:text-white hover:file:bg-[#274670] file:cursor-pointer">
                    </div>
                </div>
                <button type="submit" :disabled="!fileOk"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    {{ __('talent.submit_logbook') }}
                </button>
                <p class="text-xs text-gray-400 mt-2">{{ __('protege.ss_upload_hint') }}</p>
            </form>
        </div>
    </div>

    {{-- Logbook History --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">{{ __('talent.logbook_history') }}</h3>
        </div>

        @if($logbooks->isEmpty())
            <div class="p-8 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p>{{ __('talent.no_logbook_records') }}</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($logbooks as $lb)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                    {{ $lb->link_file_logbook ? 'bg-green-100' : 'bg-gray-100' }}">
                                    <svg class="w-5 h-5 {{ $lb->link_file_logbook ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800">{{ $lb->bulan }} {{ $lb->tahun }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        @php
                                            $lbColors = ['Dikemukakan' => 'bg-green-100 text-green-700', 'Dalam Semakan' => 'bg-blue-100 text-blue-700', 'Lewat' => 'bg-yellow-100 text-yellow-700', 'Belum Dikemukakan' => 'bg-red-100 text-red-700'];
                                            $skColors = ['Lulus' => 'bg-green-100 text-green-700', 'Dalam Proses' => 'bg-blue-100 text-blue-700', 'Perlu Semakan Semula' => 'bg-yellow-100 text-yellow-700', 'Belum Disemak' => 'bg-gray-100 text-gray-500'];
                                        @endphp
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-full font-medium {{ $lbColors[$lb->status_logbook] ?? 'bg-gray-100 text-gray-500' }}">{{ $lb->status_logbook }}</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-full font-medium {{ $skColors[$lb->status_semakan] ?? 'bg-gray-100 text-gray-500' }}">{{ $lb->status_semakan }}</span>
                                    </div>
                                    @if($lb->file_name)
                                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $lb->file_name }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if($lb->komen_mentor)
                                    <span class="text-xs text-gray-400" title="{{ $lb->komen_mentor }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                    </span>
                                @endif
                                @if($lb->link_file_logbook)
                                    @if(Str::startsWith($lb->link_file_logbook, ['http://', 'https://']))
                                        <a href="{{ $lb->link_file_logbook }}" target="_blank"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            {{ __('protege.view') }}
                                        </a>
                                    @else
                                        <a href="{{ Storage::url($lb->link_file_logbook) }}" target="_blank"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            {{ __('protege.view') }}
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
