@extends('layouts.talent')

@section('title', __('talent.training_title'))
@section('page-title', __('talent.training_title'))

@section('content')
<div class="space-y-6">

    {{-- Available Trainings to Join --}}
    @if($availableTrainings->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-[#1E3A5F] to-[#274670]">
                <h3 class="font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    {{ __('talent.available_trainings') }}
                </h3>
                <p class="text-blue-200 text-xs mt-0.5">{{ __('talent.available_trainings_hint') }}</p>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($availableTrainings as $training)
                    <div class="p-5 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-800">{{ $training->tajuk_training }}</h4>
                                <p class="text-sm text-gray-500 mt-0.5">{{ $training->nama_syarikat }} &middot; {{ $training->sesi }}</p>
                                <div class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-xs text-gray-500">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $training->tarikh_training ? \Carbon\Carbon::parse($training->tarikh_training)->format('d M Y') : '-' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $training->durasi_jam }} {{ __('talent.hours') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 font-medium">
                                        {{ $training->jenis_training }}
                                    </span>
                                    @if($training->lokasi)
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $training->lokasi }}
                                        </span>
                                    @endif
                                </div>
                                @if($training->topik_covered)
                                    <p class="text-xs text-gray-400 mt-2">{{ Str::limit($training->topik_covered, 100) }}</p>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('talent.training.join', $training) }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1E3A5F] text-white text-sm font-medium rounded-lg hover:bg-[#152c47] transition-colors flex-shrink-0"
                                        onclick="return confirm('{{ __('talent.confirm_join_training') }}')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    {{ __('talent.join_training') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- My Trainings --}}
    @if($myTrainings->isEmpty() && $availableTrainings->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <p class="text-gray-600 font-medium">{{ __('talent.no_training_records') }}</p>
            <p class="text-gray-400 text-sm mt-1">{{ __('talent.training_info_pending') }}</p>
        </div>
    @endif

    @if($myTrainings->isNotEmpty())
        {{-- Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider">{{ __('talent.total_trainings') }}</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $myTrainings->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider">{{ __('talent.completed_trainings') }}</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $myTrainings->filter(fn($t) => $t->trainingRecord?->status === 'Selesai')->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider">{{ __('talent.avg_score') }}</p>
                @php
                    $scores = $myTrainings->filter(fn($t) => $t->post_assessment_score > 0)->pluck('post_assessment_score');
                    $avg = $scores->count() > 0 ? round($scores->avg(), 1) : null;
                @endphp
                <p class="text-2xl font-bold text-[#1E3A5F] mt-1">{{ $avg ? $avg . '/10' : '--' }}</p>
            </div>
        </div>

        {{-- My training list --}}
        <h3 class="font-semibold text-gray-800 text-sm uppercase tracking-wider">{{ __('talent.my_trainings') }}</h3>
        <div class="space-y-4">
            @foreach($myTrainings as $participation)
                @php $record = $participation->trainingRecord; @endphp
                @if($record)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $record->tajuk_training }}</h3>
                                    <p class="text-sm text-gray-500 mt-0.5">{{ $record->nama_syarikat }} &middot; {{ $record->sesi }}</p>
                                </div>
                                @php
                                    $sColors = ['Selesai' => 'bg-green-100 text-green-700', 'Dalam Proses' => 'bg-yellow-100 text-yellow-700', 'Dirancang' => 'bg-blue-100 text-blue-700', 'Dibatalkan' => 'bg-red-100 text-red-700'];
                                @endphp
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $sColors[$record->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $record->status }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                                <div>
                                    <p class="text-xs text-gray-400">{{ __('talent.training_date') }}</p>
                                    <p class="text-gray-800 font-medium">{{ $record->tarikh_training ? \Carbon\Carbon::parse($record->tarikh_training)->format('d M Y') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">{{ __('talent.training_type') }}</p>
                                    <p class="text-gray-800">{{ $record->jenis_training }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">{{ __('talent.training_duration') }}</p>
                                    <p class="text-gray-800">{{ $record->durasi_jam }} {{ __('talent.hours') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">{{ __('talent.attendance_status') }}</p>
                                    @if($participation->status_kehadiran)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $participation->status_kehadiran === 'Hadir' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $participation->status_kehadiran }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>

                            @if($participation->pre_assessment_score || $participation->post_assessment_score)
                                <div class="mt-4 pt-3 border-t border-gray-100 grid grid-cols-3 gap-3 text-sm">
                                    <div>
                                        <p class="text-xs text-gray-400">{{ __('talent.pre_score') }}</p>
                                        <p class="text-gray-800 font-medium">{{ $participation->pre_assessment_score ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">{{ __('talent.post_score') }}</p>
                                        <p class="text-gray-800 font-medium">{{ $participation->post_assessment_score ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">{{ __('talent.improvement') }}</p>
                                        @if($participation->improvement_pct)
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $participation->improvement_pct >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $participation->improvement_pct > 0 ? '+' : '' }}{{ $participation->improvement_pct }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($record->topik_covered)
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <p class="text-xs text-gray-400 mb-1">{{ __('talent.topics_covered') }}</p>
                                    <p class="text-sm text-gray-600">{{ $record->topik_covered }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

</div>
@endsection
