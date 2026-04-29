@extends('layouts.admin')

@section('title', __('protege.log_title'))
@section('page-title', __('protege.log_title'))

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-gray-500">{{ __('protege.total_records', ['count' => $records->total()]) }}</p>
    </div>
    @if(in_array(\App\Http\Middleware\ModuleAccess::levelFor(auth()->user()->role?->role_name, 'logbook'), ['full', 'edit', 'own', 'create']))
    <a href="{{ route('admin.logbook.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('protege.log_add') }}
    </a>
    @endif
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('admin.logbook.index') }}" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('protege.log_search') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
        </div>
        <div class="min-w-36">
            <select name="bulan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.all_months') }}</option>
                @foreach($bulanList as $bulan)
                    <option value="{{ $bulan }}" {{ request('bulan') === $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                @endforeach
            </select>
        </div>
        @if(empty($isCompanyRole))
        <div class="min-w-40">
            <select name="id_syarikat" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.all_companies') }}</option>
                @foreach($penempatan as $s)
                    <option value="{{ $s->id_syarikat }}" {{ request('id_syarikat') == $s->id_syarikat ? 'selected' : '' }}>{{ $s->nama_syarikat }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="min-w-36">
            <select name="status_logbook" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.log_status_logbook') }}</option>
                @foreach(['Dikemukakan' => __('protege.lb_dikemukakan'), 'Dalam Semakan' => __('protege.lb_dalam_semakan'), 'Lewat' => __('protege.lb_lewat'), 'Belum Dikemukakan' => __('protege.lb_belum')] as $sl => $slLabel)
                    <option value="{{ $sl }}" {{ request('status_logbook') === $sl ? 'selected' : '' }}>{{ $slLabel }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-36">
            <select name="status_semakan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.log_status_semakan') }}</option>
                @foreach(['Lulus' => __('protege.semakan_lulus'), 'Dalam Proses' => __('protege.semakan_dalam_proses'), 'Perlu Semakan Semula' => __('protege.semakan_perlu_semula'), 'Belum Disemak' => __('protege.semakan_belum')] as $ss => $ssLabel)
                    <option value="{{ $ss }}" {{ request('status_semakan') === $ss ? 'selected' : '' }}>{{ $ssLabel }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
            {{ __('protege.filter') }}
        </button>
        @if(request()->hasAny(['search', 'bulan', 'id_syarikat', 'status_logbook', 'status_semakan']))
            <a href="{{ route('admin.logbook.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                {{ __('protege.reset') }}
            </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.id_graduan') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.nama_graduan') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">{{ __('protege.syarikat') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.bulan') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.log_status_logbook') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.log_status_semakan') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">{{ __('protege.log_nama_mentor') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.tindakan') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($records as $record)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded">{{ $record->id_graduan }}</span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $record->nama_graduan ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $record->syarikatPenempatan?->nama_syarikat ?? $record->nama_syarikat ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $record->bulan }} {{ $record->tahun }}</td>
                        <td class="px-4 py-3">
                            @php
                                $logbookColors = [
                                    'Dikemukakan' => 'bg-green-100 text-green-700',
                                    'Dalam Semakan' => 'bg-blue-100 text-blue-700',
                                    'Lewat' => 'bg-yellow-100 text-yellow-700',
                                    'Belum Dikemukakan' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            @php
                                $logbookStatusLabels = [
                                    'Dikemukakan' => __('protege.lb_dikemukakan'),
                                    'Dalam Semakan' => __('protege.lb_dalam_semakan'),
                                    'Lewat' => __('protege.lb_lewat'),
                                    'Belum Dikemukakan' => __('protege.lb_belum'),
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $logbookColors[$record->status_logbook] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $logbookStatusLabels[$record->status_logbook] ?? $record->status_logbook }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $semakanColors = [
                                    'Lulus' => 'bg-green-100 text-green-700',
                                    'Dalam Proses' => 'bg-blue-100 text-blue-700',
                                    'Perlu Semakan Semula' => 'bg-yellow-100 text-yellow-700',
                                    'Belum Disemak' => 'bg-gray-100 text-gray-600',
                                ];
                                $semakanStatusLabels = [
                                    'Lulus' => __('protege.semakan_lulus'),
                                    'Dalam Proses' => __('protege.semakan_dalam_proses'),
                                    'Perlu Semakan Semula' => __('protege.semakan_perlu_semula'),
                                    'Belum Disemak' => __('protege.semakan_belum'),
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $semakanColors[$record->status_semakan] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $semakanStatusLabels[$record->status_semakan] ?? $record->status_semakan }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden lg:table-cell">{{ $record->nama_mentor ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.logbook.show', $record) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="{{ __('protege.view') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.logbook.edit', $record) }}"
                                   class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="{{ __('protege.kemaskini') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p>{{ __('protege.no_records') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($records->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $records->links() }}
        </div>
    @endif
</div>
@endsection
