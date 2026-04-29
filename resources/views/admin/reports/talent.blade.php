@extends('layouts.admin')
@section('title', __('common.talent_report'))
@section('page-title', __('common.talent_report'))

@section('content')
<div class="flex items-center justify-between mb-5">
    <div>
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            {{ __('common.back_to_reports') }}
        </a>
        <p class="text-sm text-gray-500 mt-1">{{ count($talents) }} {{ __('common.talent_records_count') }}</p>
    </div>
    <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
        {{ __('common.export_csv') }}
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.code') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.full_name') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.university') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.programme') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">CGPA</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.status_label') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($talents as $t)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-blue-700">{{ $t->talent_code }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $t->full_name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $t->university ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $t->programme ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $t->cgpa ?? '-' }}</td>
                        <td class="px-4 py-3">@include('partials.talent-status-badge', ['status' => $t->status])</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">{{ __('messages.no_records') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
