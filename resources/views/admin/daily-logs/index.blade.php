@extends('layouts.admin')

@section('title', __('nav.daily_logs'))

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">{{ __('nav.daily_logs') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('messages.daily_log_admin_desc') }}</p>
        </div>
        <span class="text-sm text-gray-500">{{ __('messages.total') }}: {{ $records->total() }}</span>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

            {{-- Graduate (searchable) --}}
            <div x-data="{
                search: '',
                open: false,
                selectedId: '{{ request('talent_id') }}',
                selectedName: '{{ request('talent_id') ? $talents->firstWhere('id', request('talent_id'))?->full_name : '' }}',
                talents: @js($talents->map(fn($t) => ['id' => $t->id, 'name' => $t->full_name, 'code' => $t->id_graduan])),
                get filtered() {
                    if (!this.search) return this.talents.slice(0, 50);
                    const q = this.search.toLowerCase();
                    return this.talents.filter(t => t.name.toLowerCase().includes(q) || (t.code && t.code.toLowerCase().includes(q))).slice(0, 50);
                },
                select(t) { this.selectedId = t.id; this.selectedName = t.name; this.search = ''; this.open = false; },
                clear() { this.selectedId = ''; this.selectedName = ''; this.search = ''; }
            }" @click.away="open = false" class="relative">
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.talent_name') }}</label>
                <template x-if="!selectedId">
                    <input type="text" x-model="search" @focus="open = true" @input="open = true"
                           placeholder="{{ __('messages.search_placeholder') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                </template>
                <template x-if="selectedId">
                    <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2 bg-gray-50">
                        <span class="text-sm text-gray-800 truncate flex-1" x-text="selectedName"></span>
                        <button type="button" @click="clear()" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
                <div x-show="open && filtered.length > 0" x-cloak
                     class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-52 overflow-y-auto">
                    <template x-for="item in filtered" :key="item.id">
                        <button type="button" @click="select(item)"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-50 flex items-center gap-2">
                            <span class="font-mono text-xs text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded" x-text="item.code"></span>
                            <span class="text-gray-800" x-text="item.name"></span>
                        </button>
                    </template>
                </div>
                <input type="hidden" name="talent_id" :value="selectedId">
            </div>

            {{-- Implementing Company --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('protege.pelaksana') }}</label>
                <select name="id_pelaksana"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    <option value="">{{ __('messages.all') }}</option>
                    @foreach($pelaksanas as $p)
                        <option value="{{ $p->id_pelaksana }}" {{ request('id_pelaksana') === $p->id_pelaksana ? 'selected' : '' }}>
                            {{ $p->nama_syarikat }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Placement Company --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('protege.penempatan') }}</label>
                <select name="id_syarikat_penempatan"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    <option value="">{{ __('messages.all') }}</option>
                    @foreach($penempatans as $sp)
                        <option value="{{ $sp->id_syarikat }}" {{ request('id_syarikat_penempatan') === $sp->id_syarikat ? 'selected' : '' }}>
                            {{ $sp->nama_syarikat }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date From --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.date_from') }}</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            </div>

            {{-- Date To --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.date_to') }}</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            </div>

            {{-- Review Status --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.review_status_label') }}</label>
                <select name="review_status"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    <option value="">{{ __('messages.all') }}</option>
                    <option value="pending" {{ request('review_status') === 'pending' ? 'selected' : '' }}>{{ __('messages.not_reviewed') }}</option>
                    <option value="reviewed" {{ request('review_status') === 'reviewed' ? 'selected' : '' }}>{{ __('messages.reviewed') }}</option>
                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex items-end gap-2 lg:col-span-3">
                <button type="submit"
                        class="bg-[#1E3A5F] text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-[#274670] transition-colors">
                    {{ __('messages.filter') }}
                </button>
                @if(request()->hasAny(['talent_id', 'id_pelaksana', 'id_syarikat_penempatan', 'date_from', 'date_to', 'review_status']))
                    <a href="{{ route('admin.daily-logs.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-700 py-2">{{ __('messages.reset_filter') }}</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">{{ __('messages.date') }}</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">{{ __('messages.talent_name') }}</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">{{ __('messages.id_graduan') }}</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">{{ __('messages.mood_label') }}</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 hidden lg:table-cell">{{ __('messages.activities_summary') }}</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">{{ __('messages.review_status_label') }}</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($records as $log)
                        @php
                            $moodEmoji = match($log->mood) {
                                'great' => '😄', 'good' => '🙂', 'neutral' => '😐',
                                'tired' => '😓', 'difficult' => '😟', default => '📝'
                            };
                            $moodColor = match($log->mood) {
                                'great' => 'green', 'good' => 'blue', 'neutral' => 'gray',
                                'tired' => 'yellow', 'difficult' => 'red', default => 'gray'
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">
                                {{ $log->log_date->format('d M Y') }}
                                <span class="block text-xs text-gray-400">{{ $log->log_date->translatedFormat('l') }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $log->talent->full_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 hidden md:table-cell">
                                {{ $log->talent->id_graduan ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-{{ $moodColor }}-100 text-{{ $moodColor }}-700">
                                    {{ $moodEmoji }} {{ ucfirst($log->mood) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 hidden lg:table-cell max-w-xs truncate">
                                {{ Str::limit($log->activities, 60) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($log->reviewed_at)
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ __('messages.reviewed') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-500">
                                        {{ __('messages.not_reviewed') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.daily-logs.show', $log) }}"
                                   class="inline-flex items-center gap-1 text-[#1E3A5F] hover:text-[#274670] text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ __('messages.view') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">{{ __('messages.no_records_found') }}</p>
                                <p class="text-gray-400 text-sm mt-1">{{ __('messages.daily_log_no_records_desc') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($records->hasPages())
        <div>{{ $records->links() }}</div>
    @endif
</div>
@endsection
