@extends('layouts.company')
@section('title', __('company.feedback_title'))
@section('page-title', __('company.feedback_title'))

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">{{ __('company.feedback_list_title') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('company.feedback_list_desc') }}</p>
        </div>
        @if($pendingPlacements->count())
            <a href="{{ route('company.feedback.create') }}"
               class="inline-flex items-center gap-2 bg-[#1E3A5F] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#274670] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('common.submit_feedback') }}
            </a>
        @endif
    </div>

    {{-- Pending placements --}}
    @if($pendingPlacements->count())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-amber-800 mb-2">{{ __('company.pending_submit_prompt') }}</p>
            <div class="space-y-2">
                @foreach($pendingPlacements as $p)
                    <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-amber-100">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $p->talent?->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $p->department ?? '—' }} · {{ $p->start_date?->format('M Y') }}</p>
                        </div>
                        <a href="{{ route('company.feedback.create', ['placement_id' => $p->id]) }}"
                           class="text-xs bg-amber-600 text-white px-3 py-1.5 rounded-lg hover:bg-amber-700 transition-colors whitespace-nowrap">
                            {{ __('company.evaluate_now') }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Submitted feedback --}}
    <div class="space-y-3">
        @forelse($feedbacks as $feedback)
            <a href="{{ route('company.feedback.show', $feedback) }}"
               class="block bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:border-[#1E3A5F]/20 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-[#1E3A5F] flex items-center justify-center text-white font-bold flex-shrink-0">
                        {{ substr($feedback->placement?->talent?->full_name ?? '?', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800">{{ $feedback->placement?->talent?->full_name ?? '—' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $feedback->placement?->department ?? '—' }} ·
                            {{ __('company.submitted_on') }}: {{ $feedback->submitted_at?->format('d M Y') }}
                        </p>
                    </div>
                    @if($feedback->average_score)
                        <div class="text-center flex-shrink-0">
                            <p class="text-xl font-bold text-[#1E3A5F]">{{ $feedback->average_score }}</p>
                            <p class="text-xs text-gray-400">/ 5</p>
                        </div>
                    @endif
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
                <p class="text-gray-400 font-medium">{{ __('company.no_feedback_submitted_yet') }}</p>
            </div>
        @endforelse
    </div>

    @if($feedbacks->hasPages())
        <div>{{ $feedbacks->links() }}</div>
    @endif
</div>
@endsection
