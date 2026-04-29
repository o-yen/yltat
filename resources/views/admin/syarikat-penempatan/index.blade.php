@extends('layouts.admin')

@section('title', __('common.placement_company_list'))
@section('page-title', __('common.placement_company_list'))

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <p class="text-sm text-gray-500">{{ __('common.total_companies', ['count' => $penempatan->total()]) }}</p>
    <a href="{{ route('admin.syarikat-penempatan.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        {{ __('common.add_company') }}
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('common.search_name_code_sector_pic') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
        </div>
        <select name="sektor_industri" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <option value="">{{ __('common.all_sectors') }}</option>
            @foreach($sektors as $s)
                <option value="{{ $s }}" {{ request('sektor_industri') === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        <select name="status_pematuhan" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <option value="">{{ __('common.all_compliance_statuses') }}</option>
            @foreach([
                'Cemerlang' => __('common.compliance_excellent'),
                'Baik' => __('common.compliance_good'),
                'Memuaskan' => __('common.compliance_satisfactory'),
                'Sederhana' => __('common.compliance_average'),
                'Perlu Penambahbaikan' => __('common.compliance_needs_improvement'),
            ] as $value => $label)
                <option value="{{ $value }}" {{ request('status_pematuhan') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status_training" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <option value="">{{ __('common.all_training_statuses') }}</option>
            @foreach([
                'Cemerlang' => __('common.training_excellent'),
                'Baik' => __('common.training_good'),
                'Memuaskan' => __('common.training_satisfactory'),
                'Dalam Proses' => __('common.training_in_progress'),
                'Perlu Tindakan' => __('common.training_action_required'),
            ] as $value => $label)
                <option value="{{ $value }}" {{ request('status_training') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium">{{ __('common.filter') }}</button>
        @if(request()->hasAny(['search', 'sektor_industri', 'status_pematuhan', 'status_training']))
            <a href="{{ route('admin.syarikat-penempatan.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm">{{ __('common.reset') }}</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.code') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.company_name') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">{{ __('common.sector') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">{{ __('common.graduates') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.compliance_status') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">{{ __('common.training_status') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">{{ __('common.compliance_percentage') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($penempatan as $sp)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3"><span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded">{{ $sp->id_syarikat }}</span></td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $sp->nama_syarikat }}</div>
                            <div class="text-xs text-gray-400">{{ $sp->pic }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $sp->sektor_industri }}</td>
                        <td class="px-4 py-3 text-center hidden md:table-cell">
                            <span class="text-gray-700 font-medium">{{ $sp->jumlah_graduan_ditempatkan }}</span>
                            <span class="text-gray-400">/{{ $sp->kuota_dipersetujui }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $pmColor = match($sp->status_pematuhan) {
                                    'Cemerlang' => 'bg-green-100 text-green-700',
                                    'Baik' => 'bg-blue-100 text-blue-700',
                                    'Memuaskan' => 'bg-yellow-100 text-yellow-700',
                                    'Sederhana' => 'bg-yellow-100 text-yellow-700',
                                    'Perlu Penambahbaikan' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-500',
                                };
                                $pmLabel = match($sp->status_pematuhan) {
                                    'Cemerlang' => __('common.compliance_excellent'),
                                    'Baik' => __('common.compliance_good'),
                                    'Memuaskan' => __('common.compliance_satisfactory'),
                                    'Sederhana' => __('common.compliance_average'),
                                    'Perlu Penambahbaikan' => __('common.compliance_needs_improvement'),
                                    default => $sp->status_pematuhan,
                                };
                            @endphp
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $pmColor }}">{{ $pmLabel }}</span>
                        </td>
                        <td class="px-4 py-3 text-center hidden lg:table-cell">
                            @php
                                $trColor = match($sp->status_training) {
                                    'Cemerlang' => 'bg-green-100 text-green-700',
                                    'Baik' => 'bg-blue-100 text-blue-700',
                                    'Memuaskan' => 'bg-yellow-100 text-yellow-700',
                                    'Dalam Proses' => 'bg-blue-100 text-blue-700',
                                    'Perlu Tindakan' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-500',
                                };
                                $trLabel = match($sp->status_training) {
                                    'Cemerlang' => __('common.training_excellent'),
                                    'Baik' => __('common.training_good'),
                                    'Memuaskan' => __('common.training_satisfactory'),
                                    'Dalam Proses' => __('common.training_in_progress'),
                                    'Perlu Tindakan' => __('common.training_action_required'),
                                    default => $sp->status_training,
                                };
                            @endphp
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $trColor }}">{{ $trLabel }}</span>
                        </td>
                        <td class="px-4 py-3 text-center hidden lg:table-cell text-gray-700 font-medium">{{ $sp->training_compliance_pct }}%</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.syarikat-penempatan.show', $sp) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="{{ __('common.view_record') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.syarikat-penempatan.edit', $sp) }}" class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg" title="{{ __('common.edit') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.syarikat-penempatan.destroy', $sp) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg" title="{{ __('common.delete') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-12 text-center text-gray-400">{{ __('common.no_companies_found') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($penempatan->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $penempatan->links() }}</div>
    @endif
</div>
@endsection
