@extends('layouts.admin')
@section('title', __('common.intake_batch'))
@section('page-title', __('common.settings_intake_batch'))

@section('content')
<!-- Settings Nav -->
<div class="flex gap-2 mb-6 border-b border-gray-200">
    <a href="{{ route('admin.settings.users') }}" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 -mb-px">{{ __('common.users') }}</a>
    <a href="{{ route('admin.settings.roles') }}" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 -mb-px">{{ __('common.roles') }}</a>
    <a href="{{ route('admin.settings.batches') }}" class="px-4 py-2 text-sm font-medium text-[#1E3A5F] border-b-2 border-[#1E3A5F] -mb-px">{{ __('common.intake_batch') }}</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Add Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('common.add_new_batch') }}</h3>
        <form method="POST" action="{{ route('admin.settings.batches.store') }}">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.batch_name') }} *</label>
                    <input type="text" name="batch_name" value="{{ old('batch_name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                           placeholder="{{ __('common.batch_name_placeholder') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.year') }} *</label>
                    <input type="number" name="year" value="{{ old('year', date('Y')) }}" required min="2000" max="2099"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.start_date') }} *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.end_date') }} *</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('common.status_label') }}</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="planned">{{ __('common.status.planned') }}</option>
                        <option value="active" selected>{{ __('common.status.active') }}</option>
                        <option value="closed">{{ __('common.status.closed') }}</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors">{{ __('common.add_batch') }}</button>
            </div>
        </form>
    </div>

    <!-- List -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">{{ __('common.batch_list') }}</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.batch_name') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.year') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.period') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('nav.placements') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.status_label') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($batches as $batch)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $batch->batch_name }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $batch->year }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">
                            {{ $batch->start_date?->format('d/m/Y') }} - {{ $batch->end_date?->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $batch->placements_count }}</td>
                        <td class="px-4 py-3">@include('partials.talent-status-badge', ['status' => $batch->status])</td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.settings.batches.destroy', $batch) }}"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">{{ __('messages.no_batches') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($batches->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $batches->links() }}</div>
        @endif
    </div>
</div>
@endsection
