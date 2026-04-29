@extends('layouts.admin')
@section('title', __('common.user_roles'))
@section('page-title', __('common.settings_user_roles'))

@section('content')
<!-- Settings Nav -->
<div class="flex gap-2 mb-6 border-b border-gray-200">
    <a href="{{ route('admin.settings.users') }}" class="px-4 py-2 text-sm font-medium {{ request()->routeIs('admin.settings.users') ? 'text-[#1E3A5F] border-b-2 border-[#1E3A5F]' : 'text-gray-500 hover:text-gray-700' }} -mb-px">{{ __('common.users') }}</a>
    <a href="{{ route('admin.settings.roles') }}" class="px-4 py-2 text-sm font-medium {{ request()->routeIs('admin.settings.roles') ? 'text-[#1E3A5F] border-b-2 border-[#1E3A5F]' : 'text-gray-500 hover:text-gray-700' }} -mb-px">{{ __('common.roles') }}</a>
    <a href="{{ route('admin.settings.batches') }}" class="px-4 py-2 text-sm font-medium {{ request()->routeIs('admin.settings.batches') ? 'text-[#1E3A5F] border-b-2 border-[#1E3A5F]' : 'text-gray-500 hover:text-gray-700' }} -mb-px">{{ __('common.intake_batch') }}</a>
</div>

{{-- Active Roles --}}
<div class="space-y-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('common.active_roles') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($roles->where('is_active', true)->sortBy('sort_order') as $role)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-[#1E3A5F] text-white">
                            {{ $role->display_name ?? $role->role_name }}
                        </span>
                        <span class="text-xs text-gray-400">{{ __('common.users_count', ['count' => $role->users_count]) }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">{{ $role->description }}</p>
                    <div class="text-xs text-gray-400">
                        <code class="bg-gray-50 px-1.5 py-0.5 rounded">{{ $role->role_name }}</code>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Module Access Matrix --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('common.module_access_matrix') }}</h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                @php
                    $activeRoles = $roles->where('is_active', true)->sortBy('sort_order');
                    $matrix = [
                        ['module' => __('nav.dashboard'),           'access' => ['full','full','view','own','own','own']],
                        ['module' => __('nav.graduan'),             'access' => ['full','full','view','view','—','own']],
                        ['module' => __('nav.applications'),        'access' => ['full','full','—','—','—','—']],
                        ['module' => __('nav.syarikat_pelaksana'),  'access' => ['full','full','view','own','—','—']],
                        ['module' => __('nav.syarikat_penempatan'), 'access' => ['full','full','view','—','own','—']],
                        ['module' => __('nav.kehadiran'),           'access' => ['full','full','view','—','edit','—']],
                        ['module' => __('nav.daily_logs'),          'access' => ['full','full','view','—','—','create']],
                        ['module' => __('nav.logbook'),             'access' => ['full','full','view','view','edit','—']],
                        ['module' => __('nav.training'),            'access' => ['full','full','view','—','edit','—']],
                        ['module' => __('nav.isu_risiko'),          'access' => ['full','full','view','—','—','—']],
                        ['module' => __('nav.status_surat'),        'access' => ['full','full','view','edit','—','—']],
                        ['module' => __('nav.kewangan'),            'access' => ['full','full','view','edit','—','view']],
                        ['module' => __('nav.budget'),              'access' => ['full','full','view','—','—','—']],
                        ['module' => __('nav.kpi_dashboard'),       'access' => ['full','full','view','—','—','—']],
                        ['module' => __('nav.reports'),             'access' => ['full','full','view','—','—','—']],
                        ['module' => __('nav.feedback'),            'access' => ['full','full','view','—','—','create']],
                    ];
                    $accessColors = [
                        'full'   => 'bg-green-100 text-green-700',
                        'view'   => 'bg-blue-100 text-blue-700',
                        'edit'   => 'bg-amber-100 text-amber-700',
                        'own'    => 'bg-purple-100 text-purple-700',
                        'create' => 'bg-indigo-100 text-indigo-700',
                        '—'      => 'bg-gray-50 text-gray-300',
                    ];
                    $accessLabels = [
                        'full'   => __('common.access_full'),
                        'view'   => __('common.access_view'),
                        'edit'   => __('common.access_edit'),
                        'own'    => __('common.access_own'),
                        'create' => __('common.access_create'),
                        '—'      => '—',
                    ];
                @endphp
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 sticky left-0 bg-gray-50 min-w-[160px]">{{ __('common.module') }}</th>
                            @foreach($activeRoles as $role)
                                <th class="text-center px-3 py-3 font-semibold text-gray-600 min-w-[90px]">{{ $role->display_name ?? $role->role_name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($matrix as $row)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-2.5 font-medium text-gray-700 sticky left-0 bg-white">{{ $row['module'] }}</td>
                                @foreach($row['access'] as $access)
                                    <td class="px-3 py-2.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $accessColors[$access] ?? 'bg-gray-50 text-gray-300' }}">
                                            {{ $accessLabels[$access] ?? $access }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap gap-3 mt-3">
            @foreach(['full' => __('common.access_full_desc'), 'view' => __('common.access_view_desc'), 'edit' => __('common.access_edit_desc'), 'own' => __('common.access_own_desc'), 'create' => __('common.access_create_desc')] as $key => $desc)
                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $accessColors[$key] }}">{{ $accessLabels[$key] }}</span>
                    {{ $desc }}
                </div>
            @endforeach
        </div>
    </div>

    {{-- Legacy Roles (collapsed) --}}
    @if($roles->where('is_active', false)->count())
    <div>
        <details class="group">
            <summary class="cursor-pointer text-sm text-gray-400 hover:text-gray-600 flex items-center gap-2">
                <svg class="w-4 h-4 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                {{ __('common.legacy_roles') }} ({{ $roles->where('is_active', false)->count() }})
            </summary>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-3">
                @foreach($roles->where('is_active', false)->sortBy('sort_order') as $role)
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-5 opacity-60">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-gray-400 text-white">
                                {{ $role->display_name ?? $role->role_name }}
                            </span>
                            <span class="text-xs text-gray-400">{{ __('common.users_count', ['count' => $role->users_count]) }}</span>
                        </div>
                        <p class="text-xs text-gray-400">{{ $role->description }}</p>
                    </div>
                @endforeach
            </div>
        </details>
    </div>
    @endif
</div>
@endsection
