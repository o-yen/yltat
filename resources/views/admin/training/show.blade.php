@extends('layouts.admin')

@section('title', __('protege.trn_title') . ' - ' . $training->tajuk_training)
@section('page-title', __('protege.trn_details'))

@section('content')
<div class="mb-5 flex items-center justify-between">
    <a href="{{ route('admin.training.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('protege.back_to_list') }}
    </a>
    <a href="{{ route('admin.training.edit', $training) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        {{ __('protege.kemaskini') }}
    </a>
</div>

@php
    $kehadiranPct = $training->jumlah_dijemput > 0
        ? round(($training->jumlah_hadir / $training->jumlah_dijemput) * 100, 1)
        : 0;
    $improvement = ($training->pre_assessment_avg > 0 && $training->post_assessment_avg > 0)
        ? round((($training->post_assessment_avg - $training->pre_assessment_avg) / $training->pre_assessment_avg) * 100, 1)
        : null;
    $budgetUsedPct = $training->budget_allocated > 0
        ? round(($training->budget_spent / $training->budget_allocated) * 100, 1)
        : 0;

    $statusColors = [
        'Selesai' => 'bg-green-500/20 text-green-100',
        'Dalam Proses' => 'bg-blue-500/20 text-blue-100',
        'Dirancang' => 'bg-yellow-500/20 text-yellow-100',
        'Dibatalkan' => 'bg-red-500/20 text-red-100',
    ];

    $statusLabels = [
        'Selesai' => __('protege.trn_selesai'),
        'Dalam Proses' => __('protege.trn_dalam_proses'),
        'Dirancang' => __('protege.trn_dirancang'),
        'Dibatalkan' => __('protege.trn_dibatalkan'),
    ];

    $jenisLabels = [
        'Soft Skills' => __('protege.trn_soft_skills'),
        'Technical' => __('protege.trn_technical'),
        'Safety' => __('protege.trn_safety'),
        'Other' => __('protege.trn_other'),
    ];

    $trainerTypeLabels = [
        'Internal' => __('protege.trn_internal'),
        'External' => __('protege.trn_external'),
    ];
@endphp

<!-- Gradient Header -->
<div class="bg-gradient-to-r from-[#1E3A5F] to-[#2d5a8e] rounded-xl p-6 text-white mb-6 shadow-md">
    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h2 class="text-2xl font-bold">{{ $training->tajuk_training }}</h2>
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$training->status] ?? 'bg-gray-500/20 text-gray-100' }}">
                    {{ $statusLabels[$training->status] ?? $training->status }}
                </span>
            </div>
            <p class="text-blue-200">{{ $training->syarikatPenempatan->nama_syarikat ?? '-' }} &bull; {{ $training->sesi }} &bull; {{ \Carbon\Carbon::parse($training->tarikh_training)->format('d M Y') }}</p>
            <p class="text-blue-300 text-sm mt-1">{{ $jenisLabels[$training->jenis_training] ?? $training->jenis_training }} &bull; {{ $training->trainer_name }} ({{ $trainerTypeLabels[$training->trainer_type] ?? $training->trainer_type }})</p>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Kehadiran -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 {{ $kehadiranPct >= 85 ? 'bg-green-50' : ($kehadiranPct >= 70 ? 'bg-yellow-50' : 'bg-red-50') }} rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 {{ $kehadiranPct >= 85 ? 'text-green-600' : ($kehadiranPct >= 70 ? 'text-yellow-600' : 'text-red-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $kehadiranPct }}%</div>
        <div class="text-xs text-gray-500 mt-1">{{ __('protege.trn_kehadiran_pct') }} ({{ $training->jumlah_hadir }}/{{ $training->jumlah_dijemput }})</div>
    </div>

    <!-- Improvement -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 {{ $improvement !== null && $improvement >= 10 ? 'bg-green-50' : 'bg-blue-50' }} rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 {{ $improvement !== null && $improvement >= 10 ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-800">
            @if($improvement !== null)
                {{ $improvement > 0 ? '+' : '' }}{{ $improvement }}%
            @else
                -
            @endif
        </div>
        <div class="text-xs text-gray-500 mt-1">{{ __('protege.trn_improvement') }} (Pre: {{ $training->pre_assessment_avg ?? '-' }} / Post: {{ $training->post_assessment_avg ?? '-' }})</div>
    </div>

    <!-- Satisfaction -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 {{ ($training->skor_kepuasan ?? 0) >= 4 ? 'bg-green-50' : 'bg-yellow-50' }} rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 {{ ($training->skor_kepuasan ?? 0) >= 4 ? 'text-green-600' : 'text-yellow-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $training->skor_kepuasan ?? '-' }}/5</div>
        <div class="text-xs text-gray-500 mt-1">{{ __('protege.trn_kepuasan') }}</div>
    </div>

    <!-- Budget -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 {{ $budgetUsedPct <= 100 ? 'bg-emerald-50' : 'bg-red-50' }} rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 {{ $budgetUsedPct <= 100 ? 'text-emerald-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-800">RM {{ number_format($training->budget_spent ?? 0, 0, '.', ',') }}</div>
        <div class="text-xs text-gray-500 mt-1">{{ __('protege.trn_budget_used') }} / RM {{ number_format($training->budget_allocated ?? 0, 0, '.', ',') }} ({{ $budgetUsedPct }}%)</div>
    </div>
</div>

<!-- Details -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Maklumat Latihan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.trn_info') }}</h3>
        <div class="space-y-4">
            @foreach([
                [__('protege.trn_jenis'), $jenisLabels[$training->jenis_training] ?? $training->jenis_training],
                [__('protege.trn_sesi'), $training->sesi],
                [__('protege.tarikh'), \Carbon\Carbon::parse($training->tarikh_training)->format('d M Y')],
                [__('protege.trn_durasi'), ($training->durasi_jam ?? '-') . ' jam'],
                [__('protege.trn_lokasi'), $training->lokasi ?? '-'],
                [__('protege.trn_trainer'), ($training->trainer_name ?? '-') . ' (' . ($trainerTypeLabels[$training->trainer_type] ?? $training->trainer_type ?? '-') . ')'],
            ] as [$label, $value])
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</label>
                    <p class="text-gray-800 mt-1">{{ $value ?? '-' }}</p>
                </div>
            @endforeach

            @if($training->topik_covered)
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.trn_topik') }}</label>
                    <div class="mt-1 bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $training->topik_covered }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Syarikat & Catatan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.trn_company_notes') }}</h3>
        <div class="space-y-4">
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.penempatan') }}</label>
                <p class="text-gray-800 mt-1">{{ $training->syarikatPenempatan->nama_syarikat ?? '-' }}</p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.status') }}</label>
                <div class="mt-1">
                    @php
                        $statusBadge = [
                            'Selesai' => 'bg-green-100 text-green-700',
                            'Dalam Proses' => 'bg-blue-100 text-blue-700',
                            'Dirancang' => 'bg-yellow-100 text-yellow-700',
                            'Dibatalkan' => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $statusBadge[$training->status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $statusLabels[$training->status] ?? $training->status }}
                    </span>
                </div>
            </div>

            @if($training->catatan)
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('protege.catatan') }}</label>
                    <div class="mt-1 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $training->catatan }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Participant -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5" x-data="participantSelector()">
    <h3 class="font-semibold text-gray-800 mb-4">{{ __('protege.trn_add_participant') }}</h3>
    <form method="POST" action="{{ route('admin.training.add-participant', $training) }}" class="flex items-end gap-3">
        @csrf
        <div class="flex-1 relative">
            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('protege.nama_graduan') }}</label>
            <input type="text" x-model="searchQuery" @input="filterList()" @focus="filterList(); showDropdown = true"
                   placeholder="{{ __('messages.search') }}..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <div x-show="selectedId" class="mt-1.5 inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg text-xs">
                <span class="font-mono font-bold" x-text="selectedId"></span>
                <span x-text="selectedName"></span>
                <button type="button" @click="clearSelection()" class="text-blue-400 hover:text-blue-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div x-show="showDropdown && filtered.length > 0" @click.away="showDropdown = false"
                 class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                <template x-for="item in filtered" :key="item.id">
                    <button type="button" @click="selectItem(item)"
                            class="w-full text-left px-3 py-2.5 text-sm hover:bg-blue-50 border-b border-gray-50 flex items-center gap-2">
                        <span class="font-mono text-xs text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded" x-text="item.id"></span>
                        <span class="text-gray-800" x-text="item.name"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="id_graduan" :value="selectedId">
        </div>
        <button type="submit" :disabled="!selectedId"
                class="px-5 py-2 bg-[#1E3A5F] text-white rounded-lg text-sm font-medium hover:bg-[#152c47] transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex-shrink-0">
            + {{ __('protege.trn_add') }}
        </button>
    </form>
</div>

<!-- Participants Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800">{{ __('protege.trn_participant_list') }}</h3>
        @if($training->participants && $training->participants->count())
            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-semibold">
                {{ $training->participants->count() }} {{ __('protege.trn_peserta') }}
            </span>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.id_graduan') }}</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.nama_graduan') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.trn_pre_assessment') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.trn_post_assessment') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.trn_improvement') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('protege.trn_kehadiran_pct') }}</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('common.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($training->participants ?? [] as $index => $participant)
                    @php
                        $pPre = $participant->pre_assessment_score;
                        $pPost = $participant->post_assessment_score;
                        $pImprovement = ($pPre > 0 && $pPost > 0)
                            ? round((($pPost - $pPre) / $pPre) * 100, 1)
                            : null;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded">{{ $participant->id_graduan ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $participant->nama_graduan ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $pPre ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $pPost ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($pImprovement !== null)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $pImprovement >= 10 ? 'bg-green-100 text-green-700' : ($pImprovement >= 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $pImprovement > 0 ? '+' : '' }}{{ $pImprovement }}%
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($participant->status_kehadiran)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $participant->status_kehadiran === 'Hadir' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $participant->status_kehadiran }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.training.remove-participant', [$training, $participant]) }}"
                                  onsubmit="return confirm('{{ __('messages.confirm_remove_participant') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg" title="{{ __('common.delete') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            <p>{{ __('protege.trn_no_participants') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function participantSelector() {
    const allTalents = @json($talents->map(fn($t) => ['id' => $t->id_graduan, 'name' => $t->full_name])->values());
    return {
        searchQuery: '', showDropdown: false, selectedId: '', selectedName: '', filtered: [],
        filterList() {
            const q = (this.searchQuery || '').toLowerCase();
            this.filtered = allTalents.filter(t => !q || t.name.toLowerCase().includes(q) || t.id.toLowerCase().includes(q)).slice(0, 20);
            this.showDropdown = true;
        },
        selectItem(item) { this.selectedId = item.id; this.selectedName = item.name; this.searchQuery = item.name; this.showDropdown = false; },
        clearSelection() { this.selectedId = ''; this.selectedName = ''; this.searchQuery = ''; }
    };
}
</script>
@endpush
@endsection
