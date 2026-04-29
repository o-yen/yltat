@extends('layouts.admin')

@section('title', __('nav.applications'))
@section('page-title', __('nav.applications'))

@section('content')

<!-- Stats Row — Gradient Cards -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-5 mb-6">
    {{-- Pending Review --}}
    <div class="relative overflow-hidden rounded-2xl p-5 shadow-lg" style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);">
        <div class="absolute top-0 right-0 w-24 h-24 -mr-6 -mt-6 rounded-full opacity-20" style="background: rgba(255,255,255,0.3);"></div>
        <div class="relative z-10">
            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-white/80 text-xs font-medium uppercase tracking-wider">{{ __('messages.pending_review') }}</p>
            <p class="text-white text-3xl font-bold mt-1">{{ $counts['applied'] }}</p>
        </div>
    </div>

    {{-- Shortlisted --}}
    <div class="relative overflow-hidden rounded-2xl p-5 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="absolute top-0 right-0 w-24 h-24 -mr-6 -mt-6 rounded-full opacity-20" style="background: rgba(255,255,255,0.3);"></div>
        <div class="relative z-10">
            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-white/80 text-xs font-medium uppercase tracking-wider">{{ __('messages.shortlisted') }}</p>
            <p class="text-white text-3xl font-bold mt-1">{{ $counts['shortlisted'] }}</p>
        </div>
    </div>

    {{-- Total Active --}}
    <div class="relative overflow-hidden rounded-2xl p-5 shadow-lg" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
        <div class="absolute top-0 right-0 w-24 h-24 -mr-6 -mt-6 rounded-full opacity-20" style="background: rgba(255,255,255,0.3);"></div>
        <div class="relative z-10">
            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <p class="text-white/80 text-xs font-medium uppercase tracking-wider">{{ __('messages.total_active') }}</p>
            <p class="text-white text-3xl font-bold mt-1">{{ $counts['applied'] + $counts['shortlisted'] }}</p>
        </div>
    </div>

    {{-- Open Portal --}}
    <a href="{{ route('portal.register') }}" target="_blank"
       class="relative overflow-hidden rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 block" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
        <div class="absolute top-0 right-0 w-24 h-24 -mr-6 -mt-6 rounded-full opacity-20" style="background: rgba(255,255,255,0.3);"></div>
        <div class="relative z-10">
            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </div>
            <p class="text-white/80 text-xs font-medium uppercase tracking-wider">{{ __('messages.open_register_portal') }}</p>
            <p class="text-white text-lg font-bold mt-1">Portal &rarr;</p>
        </div>
    </a>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
    <form method="GET" action="{{ route('admin.applications.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('messages.search') }}</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('messages.search_placeholder') }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#274670]">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('messages.status') }}</label>
            <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#274670]">
                <option value="">{{ __('messages.all_status') }}</option>
                <option value="applied" {{ request('status') === 'applied' ? 'selected' : '' }}>{{ __('messages.pending_review') }}</option>
                <option value="shortlisted" {{ request('status') === 'shortlisted' ? 'selected' : '' }}>{{ __('messages.shortlisted') }}</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-[#274670] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#1f3a5c] transition-colors">
                {{ __('messages.filter') }}
            </button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.applications.index') }}" class="border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                {{ __('common.reset') }}
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    @if($applications->isEmpty())
        <div class="text-center py-16">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-400 text-sm">{{ __('messages.no_applications') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.code') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.name') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.ic_no') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">{{ __('messages.email') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">{{ __('messages.qualification') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">{{ __('messages.apply_date') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.status') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($applications as $i => $app)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $applications->firstItem() + $i }}</td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-[#274670] font-semibold">{{ $app->talent_code }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $app->full_name }}</div>
                            @if($app->university)
                                <div class="text-xs text-gray-400 truncate max-w-[200px]">{{ $app->university }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs font-mono">{{ $app->ic_passport_no }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs hidden md:table-cell">{{ $app->email }}</td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @php
                                $qualLabels = [
                                    'diploma'  => __('common.qual_diploma'),
                                    'ijazah'   => __('common.qual_degree'),
                                    'sarjana'  => __('common.qual_masters'),
                                    'phd'      => 'PhD',
                                    'lain'     => __('common.qual_other'),
                                ];
                            @endphp
                            <span class="text-xs text-gray-600">{{ $qualLabels[$app->highest_qualification] ?? $app->highest_qualification ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs hidden lg:table-cell">
                            {{ $app->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($app->status === 'applied')
                                <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs font-medium px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    {{ __('messages.pending') }}
                                </span>
                            @elseif($app->status === 'shortlisted')
                                <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs font-medium px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    {{ __('messages.shortlisted_label') }}
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $app->status }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.applications.show', $app) }}"
                               class="inline-flex items-center gap-1 bg-[#1E3A5F] text-white text-xs px-3 py-1.5 rounded-lg hover:bg-[#274670] transition-colors font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                {{ __('messages.review') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
        <div class="px-4 py-4 border-t border-gray-100">
            {{ $applications->links() }}
        </div>
        @endif
    @endif
</div>

@endsection
