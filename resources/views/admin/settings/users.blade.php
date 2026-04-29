@extends('layouts.admin')
@section('title', __('common.user_management'))
@section('page-title', __('common.settings_user_management'))

@section('content')
<!-- Settings Nav -->
<div class="flex gap-2 mb-6 border-b border-gray-200">
    <a href="{{ route('admin.settings.users') }}" class="px-4 py-2 text-sm font-medium {{ request()->routeIs('admin.settings.users') ? 'text-[#1E3A5F] border-b-2 border-[#1E3A5F]' : 'text-gray-500 hover:text-gray-700' }} -mb-px">{{ __('common.users') }}</a>
    <a href="{{ route('admin.settings.roles') }}" class="px-4 py-2 text-sm font-medium {{ request()->routeIs('admin.settings.roles') ? 'text-[#1E3A5F] border-b-2 border-[#1E3A5F]' : 'text-gray-500 hover:text-gray-700' }} -mb-px">{{ __('common.roles') }}</a>
    <a href="{{ route('admin.settings.batches') }}" class="px-4 py-2 text-sm font-medium {{ request()->routeIs('admin.settings.batches') ? 'text-[#1E3A5F] border-b-2 border-[#1E3A5F]' : 'text-gray-500 hover:text-gray-700' }} -mb-px">{{ __('common.intake_batch') }}</a>
</div>

<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-gray-500">{{ __('common.registered_users_count', ['count' => $users->total()]) }}</p>
    <a href="{{ route('admin.settings.users.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        {{ __('common.add_user') }}
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-center">
        <div class="flex-1 min-w-64">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_placeholder') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
        </div>
        <button type="submit" class="px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium">{{ __('messages.filter') }}</button>
        @if(request()->filled('search'))
            <a href="{{ route('admin.settings.users') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm">{{ __('common.reset') }}</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.name') }}</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.email') }}</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.role') }}</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.account_link') }}</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.status_label') }}</th>
                <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ __('common.action') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}"
                                     class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-8 h-8 rounded-full bg-[#1E3A5F] flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="font-medium text-gray-800">{{ $user->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                    <td class="px-4 py-3">
                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-medium">
                            {{ $user->role?->display_name ?? $user->role?->role_name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        @if($user->talent)
                            <div class="font-medium text-gray-800">{{ $user->talent->full_name }}</div>
                            <div class="text-xs text-gray-400">{{ $user->talent->talent_code }}</div>
                        @elseif($user->company)
                            <div class="font-medium text-gray-800">{{ $user->company->company_name }}</div>
                            <div class="text-xs text-gray-400">{{ $user->company->company_code }}</div>
                        @elseif($user->syarikatPelaksana)
                            <div class="font-medium text-gray-800">{{ $user->syarikatPelaksana->nama_syarikat }}</div>
                            <div class="text-xs text-gray-400">{{ $user->syarikatPelaksana->id_pelaksana }}</div>
                        @elseif($user->syarikatPenempatan)
                            <div class="font-medium text-gray-800">{{ $user->syarikatPenempatan->nama_syarikat }}</div>
                            <div class="text-xs text-gray-400">{{ $user->syarikatPenempatan->id_syarikat }}</div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">@include('partials.talent-status-badge', ['status' => $user->status])</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('admin.settings.users.edit', $user) }}" class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg" title="{{ __('messages.edit') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.settings.users.reset-password', $user) }}" onsubmit="return confirm('{{ __('messages.confirm_reset_password', ['name' => $user->full_name]) }}')">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg" title="{{ __('messages.reset_password') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.settings.users.destroy', $user) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg" title="{{ __('messages.delete') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if($users->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $users->links() }}</div>
    @endif
</div>
@endsection
