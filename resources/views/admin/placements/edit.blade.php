@extends('layouts.admin')
@section('title', __('common.edit_placement'))
@section('page-title', __('common.edit_placement'))

@section('content')
<div class="max-w-3xl">
    <div class="mb-5">
        <a href="{{ route('admin.placements.show', $placement) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            {{ __('messages.back') }}
        </a>
    </div>

    <form method="POST" action="{{ route('admin.placements.update', $placement) }}">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.talent') }} *</label>
                    <select name="talent_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        @foreach($talents as $t)
                            <option value="{{ $t->id }}" {{ old('talent_id', $placement->talent_id) == $t->id ? 'selected' : '' }}>{{ $t->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.company_name_short') }} *</label>
                    <select name="company_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}" {{ old('company_id', $placement->company_id) == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.intake_batch') }}</label>
                    <select name="batch_id" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="">{{ __('common.none') }}</option>
                        @foreach($batches as $b)
                            <option value="{{ $b->id }}" {{ old('batch_id', $placement->batch_id) == $b->id ? 'selected' : '' }}>{{ $b->batch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.department') }}</label>
                    <input type="text" name="department" value="{{ old('department', $placement->department) }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.supervisor_name') }}</label>
                    <input type="text" name="supervisor_name" value="{{ old('supervisor_name', $placement->supervisor_name) }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.supervisor_email') }}</label>
                    <input type="email" name="supervisor_email" value="{{ old('supervisor_email', $placement->supervisor_email) }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.start_date') }} *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $placement->start_date?->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.end_date') }} *</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $placement->end_date?->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.monthly_stipend_rm') }} *</label>
                    <input type="number" name="monthly_stipend" value="{{ old('monthly_stipend', $placement->monthly_stipend) }}" min="0" step="0.01" inputmode="decimal" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.additional_cost_rm') }}</label>
                    <input type="number" name="additional_cost" value="{{ old('additional_cost', $placement->additional_cost) }}" min="0" step="0.01" inputmode="decimal"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.status_label') }} *</label>
                    <select name="placement_status" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        @foreach(['planned', 'confirmed', 'active', 'extended', 'completed', 'terminated', 'cancelled'] as $val)
                            <option value="{{ $val }}" {{ old('placement_status', $placement->placement_status) === $val ? 'selected' : '' }}>{{ __('common.status.' . $val) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.programme_type') }}</label>
                    <input type="text" name="programme_type" value="{{ old('programme_type', $placement->programme_type) }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.remarks') }}</label>
                    <textarea name="remarks" rows="3"
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">{{ old('remarks', $placement->remarks) }}</textarea>
                </div>
            </div>
        </div>
        <div class="mt-5 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors">{{ __('common.save_changes') }}</button>
            <a href="{{ route('admin.placements.show', $placement) }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
