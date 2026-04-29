@extends('layouts.admin')
@section('title', __('common.add_placement'))
@section('page-title', __('common.add_placement_new'))

@section('content')
<div class="max-w-3xl">
    <div class="mb-5">
        <a href="{{ route('admin.placements.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            {{ __('common.back_to_list') }}
        </a>
    </div>

    <form method="POST" action="{{ route('admin.placements.store') }}">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.talent') }} *</label>
                    <select name="talent_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('talent_id') border-red-400 @enderror">
                        <option value="">{{ __('common.select_talent') }}</option>
                        @foreach($talents as $t)
                            <option value="{{ $t->id }}" {{ old('talent_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->full_name }} ({{ $t->talent_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('talent_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.company_name_short') }} *</label>
                    <select name="company_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('company_id') border-red-400 @enderror">
                        <option value="">{{ __('common.select_company') }}</option>
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}" {{ old('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.intake_batch') }}</label>
                    <select name="batch_id" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('common.select_batch') }}</option>
                        @foreach($batches as $b)
                            <option value="{{ $b->id }}" {{ old('batch_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->batch_name }} ({{ $b->year }})
                            </option>
                        @endforeach
                    </select>
                    @if($batches->isEmpty())
                        <p class="text-xs text-amber-600 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            {{ __('messages.no_batch_create_first') }} <a href="{{ route('admin.settings.batches') }}" class="underline font-medium hover:text-amber-700" target="_blank">{{ __('messages.create_batch_link') }} →</a>
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.department') }}</label>
                    <input type="text" name="department" value="{{ old('department') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.supervisor_name') }}</label>
                    <input type="text" name="supervisor_name" value="{{ old('supervisor_name') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.supervisor_email') }}</label>
                    <input type="email" name="supervisor_email" value="{{ old('supervisor_email') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.start_date') }} *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('start_date') border-red-400 @enderror">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.end_date') }} *</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] @error('end_date') border-red-400 @enderror">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.duration_months') }}</label>
                    <input type="number" name="duration_months" value="{{ old('duration_months') }}" min="1" max="24" step="1" inputmode="numeric" data-numeric="integer"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.monthly_stipend_rm') }} *</label>
                    <input type="number" name="monthly_stipend" value="{{ old('monthly_stipend', 0) }}" min="0" step="0.01" inputmode="decimal" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.additional_cost_rm') }}</label>
                    <input type="number" name="additional_cost" value="{{ old('additional_cost', 0) }}" min="0" step="0.01" inputmode="decimal"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.programme_type') }}</label>
                    <input type="text" name="programme_type" value="{{ old('programme_type') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                           placeholder="{{ __('common.programme_type_placeholder') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.placement_status') }} *</label>
                    <select name="placement_status" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        @foreach(['planned', 'confirmed', 'active', 'extended', 'completed', 'terminated', 'cancelled'] as $val)
                            <option value="{{ $val }}" {{ old('placement_status', 'planned') === $val ? 'selected' : '' }}>{{ __('common.status.' . $val) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.remarks') }}</label>
                    <textarea name="remarks" rows="3"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>
        <div class="mt-5 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors shadow-sm">{{ __('common.save_placement') }}</button>
            <a href="{{ route('admin.placements.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
