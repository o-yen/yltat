@extends('layouts.admin')

@section('title', __('nav.graduan'))
@section('page-title', __('nav.graduan'))

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-gray-500">{{ __('common.total_records', ['count' => $talents->total()]) }}</p>
    </div>
    <a href="{{ route('admin.talents.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('common.add_new_talent') }}
    </a>
</div>

<!-- Overview Cards -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-5">
    {{-- Total --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500">{{ __('common.total_graduates') }}</p>
            </div>
        </div>
    </div>

    {{-- Active --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-green-600">{{ $stats['aktif'] }}</p>
                <p class="text-xs text-gray-500">{{ __('common.status.Aktif') }}</p>
            </div>
        </div>
    </div>

    {{-- Completed --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['tamat'] }}</p>
                <p class="text-xs text-gray-500">{{ __('common.status.Tamat') }}</p>
            </div>
        </div>
    </div>

    {{-- Applied --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-amber-600">{{ $stats['applied'] }}</p>
                <p class="text-xs text-gray-500">{{ __('common.status.applied') }}</p>
            </div>
        </div>
    </div>

    {{-- Early Termination --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-red-500">{{ $stats['berhenti'] }}</p>
                <p class="text-xs text-gray-500">{{ __('common.status.Berhenti Awal') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Category Breakdown (if data exists) --}}
@if($categories->count())
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">{{ __('common.category_breakdown') }}</h3>
    <div class="flex flex-wrap gap-3">
        @foreach($categories as $cat => $count)
            <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                <span class="w-2 h-2 rounded-full {{ match($cat) { 'Anak ATM' => 'bg-blue-500', 'Anak Veteran' => 'bg-emerald-500', 'Anak Awam MINDEF' => 'bg-purple-500', default => 'bg-gray-400' } }}"></span>
                <span class="text-sm text-gray-700">{{ $cat }}</span>
                <span class="text-sm font-bold text-gray-800">{{ $count }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('admin.talents.index') }}" class="flex flex-wrap gap-3">
        {{-- Preserve filter params from redirections --}}
        @if(request('id_pelaksana'))
            <input type="hidden" name="id_pelaksana" value="{{ request('id_pelaksana') }}">
        @endif
        @if(request('id_syarikat_penempatan'))
            <input type="hidden" name="id_syarikat_penempatan" value="{{ request('id_syarikat_penempatan') }}">
        @endif
        @if(request('kategori'))
            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
        @endif

        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('common.search_name_code_email') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
        </div>
        <div class="min-w-36">
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('common.all_statuses') }}</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ __('common.status.' . $status) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-40">
            <input type="text" name="university" value="{{ request('university') }}"
                   placeholder="{{ __('common.university_placeholder') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
        </div>
        <button type="submit" class="px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
            {{ __('messages.filter') }}
        </button>
        @if(request()->hasAny(['search', 'status', 'university', 'id_pelaksana', 'id_syarikat_penempatan', 'kategori']))
            <a href="{{ route('admin.talents.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                {{ __('common.reset') }}
            </a>
        @endif

        {{-- Show active filter badge --}}
        @if(request('id_pelaksana'))
            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs rounded-lg font-medium">
                {{ __('protege.pelaksana') }}: {{ request('id_pelaksana') }}
                <a href="{{ route('admin.talents.index') }}" class="text-blue-400 hover:text-blue-600">&times;</a>
            </span>
        @endif
        @if(request('id_syarikat_penempatan'))
            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs rounded-lg font-medium">
                {{ __('protege.penempatan') }}: {{ request('id_syarikat_penempatan') }}
                <a href="{{ route('admin.talents.index') }}" class="text-blue-400 hover:text-blue-600">&times;</a>
            </span>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('common.code') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('common.full_name') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">{{ __('common.university') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">{{ __('common.programme') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">{{ __('common.cgpa') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('common.status_label') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('common.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($talents as $talent)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded">{{ $talent->talent_code }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $talent->full_name }}</div>
                            <div class="text-xs text-gray-400">{{ $talent->email }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $talent->university ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 hidden lg:table-cell">{{ $talent->programme ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 hidden lg:table-cell">{{ $talent->cgpa ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @include('partials.talent-status-badge', ['status' => $talent->resolved_status])
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.talents.show', $talent) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="{{ __('messages.view') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if(in_array(\App\Http\Middleware\ModuleAccess::levelFor(auth()->user()->role?->role_name, 'talents'), ['full', 'edit', 'own', 'create']))
                                <a href="{{ route('admin.talents.edit', $talent) }}"
                                   class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="{{ __('messages.edit') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.talents.destroy', $talent) }}"
                                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="{{ __('messages.delete') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                            </svg>
                            <p>{{ __('messages.no_records') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($talents->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $talents->links() }}
        </div>
    @endif
</div>
@endsection
