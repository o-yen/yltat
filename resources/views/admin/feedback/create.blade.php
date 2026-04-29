@extends('layouts.admin')
@section('title', __('common.add_feedback'))
@section('page-title', __('common.add_feedback'))

@section('content')
<div class="max-w-2xl">
    <div class="mb-5">
        <a href="{{ route('admin.feedback.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            {{ __('common.back_to_list') }}
        </a>
    </div>

    <form method="POST" action="{{ route('admin.feedback.store') }}">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="space-y-5">
                <div>
                    @if($placementLocked && $selectedPlacement)
                        <input type="hidden" name="placement_id" value="{{ $selectedPlacement->id }}">
                    @else
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.placement') }} *</label>
                        <select name="placement_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('placement_id') border-red-400 @enderror">
                            <option value="">{{ __('messages.select_placement') }}</option>
                            @foreach($placements as $p)
                                <option value="{{ $p->id }}" {{ (old('placement_id') == $p->id || ($selectedPlacement && $selectedPlacement->id == $p->id)) ? 'selected' : '' }}>
                                    {{ $p->talent?->full_name }} @ {{ $p->company?->company_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('placement_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <div>
                    @if($feedbackSourceLocked)
                        <input type="hidden" name="feedback_from" value="{{ old('feedback_from', $defaultFeedbackSource) }}">
                    @else
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.feedback_source') }} *</label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach($feedbackSourceOptions as $val => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="feedback_from" value="{{ $val }}" {{ old('feedback_from', $defaultFeedbackSource) === $val ? 'checked' : '' }} class="sr-only peer">
                                    <div class="p-3 border-2 rounded-lg text-center text-sm font-medium transition-all peer-checked:border-[#1E3A5F] peer-checked:bg-blue-50 peer-checked:text-[#1E3A5F] border-gray-200 text-gray-600 hover:border-gray-300">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('feedback_from')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Score Fields -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('common.rating_scale_label') }}</label>
                    <div class="space-y-4">
                        @foreach([
                            ['score_technical', __('common.score_technical')],
                            ['score_communication', __('common.score_communication')],
                            ['score_discipline', __('common.score_discipline')],
                            ['score_problem_solving', __('common.score_problem_solving')],
                            ['score_professionalism', __('common.score_professionalism')],
                        ] as [$field, $label])
                            <div class="flex items-center gap-4">
                                <div class="w-48 text-sm text-gray-700">{{ $label }}</div>
                                <div class="flex gap-2">
                                    @foreach([1, 2, 3, 4, 5] as $score)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="{{ $field }}" value="{{ $score }}" {{ old($field) == $score ? 'checked' : '' }} class="sr-only peer">
                                            <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center text-sm font-bold transition-all
                                                peer-checked:border-[#1E3A5F] peer-checked:bg-[#1E3A5F] peer-checked:text-white
                                                border-gray-200 text-gray-500 hover:border-gray-400">
                                                {{ $score }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.comments') }}</label>
                    <textarea name="comments" rows="4"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                              placeholder="{{ __('messages.comments_placeholder') }}">{{ old('comments') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-5 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors shadow-sm">{{ __('common.submit_feedback') }}</button>
            <a href="{{ route('admin.feedback.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
