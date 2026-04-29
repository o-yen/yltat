@extends('layouts.admin')
@section('title', __('common.feedback_info'))
@section('page-title', __('common.feedback_info'))

@section('content')
<div class="max-w-2xl">
    <div class="mb-5">
        <a href="{{ route('admin.feedback.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            {{ __('common.back_to_list') }}
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <!-- Header -->
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-800">{{ $feedback->placement?->talent?->full_name }}</h2>
                <p class="text-gray-500 text-sm">{{ $feedback->placement?->company?->company_name }}</p>
                <p class="text-gray-400 text-xs mt-1">{{ $feedback->submitted_at?->format('d/m/Y H:i') }}</p>
            </div>
            @php
                $colors = ['company' => 'bg-blue-100 text-blue-700', 'talent' => 'bg-green-100 text-green-700', 'yltat' => 'bg-purple-100 text-purple-700'];
                $labels = [
                    'company' => __('common.feedback_from_company'),
                    'talent' => __('common.feedback_from_talent'),
                    'yltat' => __('common.feedback_from_yltat'),
                ];
            @endphp
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold {{ $colors[$feedback->feedback_from] ?? 'bg-gray-100 text-gray-700' }}">
                {{ $labels[$feedback->feedback_from] ?? $feedback->feedback_from }}
            </span>
        </div>

        <!-- Scores -->
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ __('common.ratings') }}</h3>
            <div class="grid grid-cols-1 gap-3">
                @foreach([
                    [__('common.score_technical'), $feedback->score_technical],
                    [__('common.score_communication'), $feedback->score_communication],
                    [__('common.score_discipline'), $feedback->score_discipline],
                    [__('common.score_problem_solving'), $feedback->score_problem_solving],
                    [__('common.score_professionalism'), $feedback->score_professionalism],
                ] as [$label, $score])
                    @if($score)
                        <div class="flex items-center gap-4">
                            <div class="w-48 text-sm text-gray-600">{{ $label }}</div>
                            <div class="flex gap-1.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                        {{ $i <= $score ? ($score >= 4 ? 'bg-green-500 text-white' : ($score >= 3 ? 'bg-yellow-500 text-white' : 'bg-red-500 text-white')) : 'bg-gray-100 text-gray-400' }}">
                                        {{ $i }}
                                    </div>
                                @endfor
                                <div class="ml-2 flex items-center text-sm font-bold {{ $score >= 4 ? 'text-green-700' : ($score >= 3 ? 'text-yellow-700' : 'text-red-700') }}">
                                    {{ $score }}/5
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            @if($feedback->average_score)
                <div class="mt-4 p-4 bg-gray-50 rounded-xl flex items-center justify-between">
                    <span class="font-semibold text-gray-700">{{ __('common.average_score') }}</span>
                    <span class="text-2xl font-bold {{ $feedback->average_score >= 4 ? 'text-green-700' : ($feedback->average_score >= 3 ? 'text-yellow-700' : 'text-red-700') }}">
                        {{ number_format($feedback->average_score, 2) }}/5
                    </span>
                </div>
            @endif
        </div>

        @if($feedback->comments)
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ __('common.comments') }}</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700 text-sm leading-relaxed">{{ $feedback->comments }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
