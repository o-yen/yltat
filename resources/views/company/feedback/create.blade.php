@extends('layouts.company')
@section('title', __('common.submit_feedback'))
@section('page-title', __('common.submit_feedback'))

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

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">{{ __('company.trainee_evaluation') }}</h2>
        <p class="text-sm text-gray-500 mb-6">{{ __('company.trainee_evaluation_desc') }}</p>

        <form method="POST" action="{{ route('company.feedback.store') }}" class="space-y-6">
            @csrf

            {{-- Placement selector --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('company.trainee_or_placement') }} <span class="text-red-500">*</span></label>
                @if($placements->isEmpty())
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-500">
                        {{ __('company.no_active_placements_requiring_evaluation') }}
                    </div>
                @else
                    <select name="placement_id" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('placement_id') border-red-400 @enderror">
                        <option value="">{{ __('company.select_trainee') }}</option>
                        @foreach($placements as $p)
                            <option value="{{ $p->id }}" {{ (old('placement_id', $selectedPlacement?->id) == $p->id) ? 'selected' : '' }}>
                                {{ $p->talent?->full_name }} ({{ $p->department ?? __('company.no_department') }} · {{ $p->start_date?->format('M Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('placement_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @endif
            </div>

            @if(! $placements->isEmpty())
                {{-- Score fields --}}
                @php
                    $criteria = [
                        'score_technical'       => [__('common.score_technical'), __('company.technical_skills_desc')],
                        'score_communication'   => [__('common.score_communication'), __('company.communication_desc')],
                        'score_discipline'      => [__('company.discipline_attendance'), __('company.discipline_attendance_desc')],
                        'score_problem_solving' => [__('common.score_problem_solving'), __('company.problem_solving_desc')],
                        'score_professionalism' => [__('common.score_professionalism'), __('company.professionalism_desc')],
                    ];
                @endphp

                @foreach($criteria as $field => [$label, $desc])
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">{{ $label }}</label>
                        <p class="text-xs text-gray-400 mb-2">{{ $desc }}</p>
                        @error($field)
                            <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
                        @enderror
                        <div class="flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" name="{{ $field }}" value="{{ $i }}" class="sr-only peer"
                                           {{ old($field) == $i ? 'checked' : '' }}>
                                    <div class="flex flex-col items-center gap-1 py-2 px-1 rounded-lg border-2 border-gray-200 peer-checked:border-[#1E3A5F] peer-checked:bg-blue-50 hover:border-gray-300 transition-colors text-center">
                                        <span class="text-lg font-bold text-gray-700 peer-checked:text-[#1E3A5F]">{{ $i }}</span>
                                        <span class="text-xs text-gray-400 leading-tight">
                                            {{ match($i) { 1 => __('company.weak'), 2 => __('company.fair'), 3 => __('company.good'), 4 => __('company.very_good'), 5 => __('company.excellent') } }}
                                        </span>
                                    </div>
                                </label>
                            @endfor
                        </div>
                    </div>
                @endforeach

                {{-- Comments --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('company.overall_comments') }}</label>
                    <textarea name="comments" rows="4"
                              placeholder="{{ __('company.overall_comments_placeholder') }}"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] resize-none">{{ old('comments') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <button type="submit"
                            class="bg-[#1E3A5F] text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-[#274670] transition-colors">
                        {{ __('company.submit_evaluation') }}
                    </button>
                    <a href="{{ route('company.feedback.index') }}"
                       class="px-4 py-2.5 rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition-colors">
                        {{ __('messages.cancel') }}
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
