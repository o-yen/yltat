@extends('layouts.admin')
@section('title', __('nav.manage_placement'))
@section('page-title', __('nav.manage_placement'))

@section('content')
<div class="mb-6">
    <p class="text-sm text-gray-500">{{ __('protege.total_records', ['count' => $talents->total()]) }}</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('messages.search') }}..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
        </div>
        <select name="assignment" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <option value="">{{ __('common.all') }}</option>
            <option value="assigned" {{ request('assignment') === 'assigned' ? 'selected' : '' }}>{{ __('common.assigned') }}</option>
            <option value="not_assigned" {{ request('assignment') === 'not_assigned' ? 'selected' : '' }}>{{ __('common.not_assigned') }}</option>
        </select>
        <select name="status_aktif" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <option value="">{{ __('common.all_status') }}</option>
            @foreach(['Aktif', 'Tamat', 'Dalam Proses', 'Berhenti Awal'] as $sa)
                <option value="{{ $sa }}" {{ request('status_aktif') === $sa ? 'selected' : '' }}>{{ $sa }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium">{{ __('messages.filter') }}</button>
        @if(request()->hasAny(['search', 'assignment', 'status_aktif']))
            <a href="{{ route('admin.manage-placement.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm">{{ __('protege.reset') }}</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.id_graduan') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('protege.nama_graduan') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">{{ __('common.implementing_company') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">{{ __('common.placement_company') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.status_label') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.assignment_status') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($talents as $talent)
                    @php
                        $isAssigned = !empty($talent->id_syarikat_penempatan);
                        $statusColors = [
                            'Aktif' => 'bg-green-100 text-green-700',
                            'Tamat' => 'bg-gray-100 text-gray-600',
                            'Dalam Proses' => 'bg-yellow-100 text-yellow-700',
                            'Berhenti Awal' => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded">{{ $talent->id_graduan }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $talent->full_name }}</div>
                            <div class="text-xs text-gray-400">{{ $talent->university ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $talent->syarikatPelaksana?->nama_syarikat ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $talent->syarikatPenempatan?->nama_syarikat ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$talent->status_aktif] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $talent->status_aktif ?? '-' }}
                            </span>
                            @if($talent->status_aktif === 'Tamat' && $talent->status_penyerapan_6bulan)
                                @php
                                    $abColors = ['Diserap' => 'text-green-600', 'Tidak Diserap' => 'text-red-600', 'Dalam Proses' => 'text-amber-600', 'Belum Layak' => 'text-gray-500'];
                                @endphp
                                <div class="text-[10px] mt-0.5 font-medium {{ $abColors[$talent->status_penyerapan_6bulan] ?? 'text-gray-500' }}">{{ $talent->status_penyerapan_6bulan }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($isAssigned)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ __('common.assigned') }}</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">{{ __('common.not_assigned') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.manage-placement.show', $talent) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#1E3A5F] text-white rounded-lg text-xs font-medium hover:bg-[#152c47] transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                {{ __('common.manage') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">{{ __('protege.no_records') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($talents->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $talents->links() }}</div>
    @endif
</div>
@endsection
