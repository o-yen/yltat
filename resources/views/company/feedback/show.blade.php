@extends('layouts.company')
@section('title', __('company.feedback_details_title'))
@section('page-title', __('company.feedback_details_title'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('company.feedback.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('messages.back') }}
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-[#1E3A5F] to-[#274670] px-6 py-5">
            <p class="text-blue-200 text-xs uppercase tracking-wide">{{ __('company.feedback_company_label') }}</p>
            <h2 class="text-white text-xl font-bold mt-0.5">{{ $feedback->placement?->talent?->full_name ?? '—' }}</h2>
            <p class="text-blue-300 text-sm mt-0.5">
                {{ $feedback->placement?->department ?? '—' }} ·
                {{ __('company.submitted_on') }} {{ $feedback->submitted_at?->format('d M Y') }}
            </p>
            @if($feedback->average_score)
                <div class="mt-3 inline-block bg-white/20 px-4 py-1.5 rounded-full">
                    <span class="text-white font-bold text-lg">{{ $feedback->average_score }}</span>
                    <span class="text-blue-200 text-sm"> {{ __('company.average_out_of_five') }}</span>
                </div>
            @endif
        </div>

        <div class="p-6 space-y-5">
            @php
                $criteria = [
                    'score_technical'       => 'Kemahiran Teknikal',
                    'score_communication'   => 'Komunikasi',
                    'score_discipline'      => 'Disiplin & Kehadiran',
                    'score_problem_solving' => 'Penyelesaian Masalah',
                    'score_professionalism' => 'Profesionalisme',
                ];
            @endphp

            <div class="space-y-3">
                @foreach($criteria as $field => $label)
                    @php $score = $feedback->$field; @endphp
                    <div class="flex items-center gap-3">
                        <p class="text-sm text-gray-600 w-44 flex-shrink-0">{{ $label }}</p>
                        <div class="flex gap-1.5 flex-1">
                            @for($i = 1; $i <= 5; $i++)
                                <div class="h-3 flex-1 rounded-full {{ $score && $i <= $score ? 'bg-[#1E3A5F]' : 'bg-gray-200' }}"></div>
                            @endfor
                        </div>
                        <span class="text-sm font-bold text-gray-700 w-8 text-right">{{ $score ? $score.'/5' : '—' }}</span>
                    </div>
                @endforeach
            </div>

            @if($feedback->comments)
                <div class="pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">{{ __('common.comments') }}</p>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 leading-relaxed">
                        {{ $feedback->comments }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
