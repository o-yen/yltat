@extends('layouts.admin')

@section('title', __('protege.trn_title'))
@section('page-title', __('protege.trn_title'))

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-gray-500">{{ __('protege.total_records', ['count' => $records->total()]) }}</p>
    </div>
    @if(in_array(\App\Http\Middleware\ModuleAccess::levelFor(auth()->user()->role?->role_name, 'training'), ['full', 'edit', 'own', 'create']))
    <a href="{{ route('admin.training.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('protege.trn_add') }}
    </a>
    @endif
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('admin.training.index') }}" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('protege.trn_search') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
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
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.all_statuses') }}</option>
                @foreach(['Selesai' => __('protege.trn_selesai'), 'Dalam Proses' => __('protege.trn_dalam_proses'), 'Dirancang' => __('protege.trn_dirancang'), 'Dibatalkan' => __('protege.trn_dibatalkan')] as $st => $stLabel)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $stLabel }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-36">
            <select name="sesi" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                <option value="">{{ __('protege.all') }}</option>
                <option value="Session 1" {{ request('sesi') === 'Session 1' ? 'selected' : '' }}>Session 1</option>
                <option value="Session 2" {{ request('sesi') === 'Session 2' ? 'selected' : '' }}>Session 2</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
            {{ __('protege.filter') }}
        </button>
        @if(request()->hasAny(['search', 'id_syarikat', 'status', 'sesi']))
            <a href="{{ route('admin.training.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
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
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.trn_id') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.syarikat') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.trn_tajuk') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.trn_sesi') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.tarikh') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.trn_kehadiran_pct') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">{{ __('protege.trn_improvement') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.status') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.tindakan') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($records as $record)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded">{{ $record->id_training ?? $record->id }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $record->syarikatPenempatan?->nama_syarikat ?? $record->nama_syarikat ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $record->tajuk_training }}</div>
                            @if($record->participants_count ?? false)
                                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-full text-xs font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $record->participants_count }} {{ __('protege.trn_peserta') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $record->sesi }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ \Carbon\Carbon::parse($record->tarikh_training)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $kehadiranPct = $record->jumlah_dijemput > 0
                                    ? round(($record->jumlah_hadir / $record->jumlah_dijemput) * 100)
                                    : 0;
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $kehadiranPct >= 85 ? 'bg-green-100 text-green-700' : ($kehadiranPct >= 70 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ $kehadiranPct }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center hidden lg:table-cell">
                            @php
                                $improvement = ($record->pre_assessment_avg > 0 && $record->post_assessment_avg > 0)
                                    ? round((($record->post_assessment_avg - $record->pre_assessment_avg) / $record->pre_assessment_avg) * 100, 1)
                                    : null;
                            @endphp
                            @if($improvement !== null)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $improvement >= 10 ? 'bg-green-100 text-green-700' : ($improvement >= 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $improvement > 0 ? '+' : '' }}{{ $improvement }}%
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $statusColors = [
                                    'Selesai' => 'bg-green-100 text-green-700',
                                    'Dalam Proses' => 'bg-blue-100 text-blue-700',
                                    'Dirancang' => 'bg-yellow-100 text-yellow-700',
                                    'Dibatalkan' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            @php
                                $statusLabels = [
                                    'Selesai' => __('protege.trn_selesai'),
                                    'Dalam Proses' => __('protege.trn_dalam_proses'),
                                    'Dirancang' => __('protege.trn_dirancang'),
                                    'Dibatalkan' => __('protege.trn_dibatalkan'),
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $statusLabels[$record->status] ?? $record->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.training.show', $record) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="{{ __('protege.view') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.training.edit', $record) }}"
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
                        <td colspan="9" class="px-4 py-12 text-center text-gray-400">
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
