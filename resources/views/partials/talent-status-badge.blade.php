@php
$colors = [
    'applied' => 'bg-gray-100 text-gray-700',
    'shortlisted' => 'bg-yellow-100 text-yellow-700',
    'approved' => 'bg-blue-100 text-blue-700',
    'assigned' => 'bg-indigo-100 text-indigo-700',
    'in_progress' => 'bg-green-100 text-green-700',
    'completed' => 'bg-emerald-100 text-emerald-700',
    'alumni' => 'bg-purple-100 text-purple-700',
    'inactive' => 'bg-red-100 text-red-700',
    'Aktif' => 'bg-green-100 text-green-700',
    'Tamat' => 'bg-slate-100 text-slate-700',
    'Berhenti Awal' => 'bg-red-100 text-red-700',
    'Diserap' => 'bg-emerald-100 text-emerald-700',
    'Tidak Diserap' => 'bg-amber-100 text-amber-700',
    'Belum Layak' => 'bg-gray-100 text-gray-700',
    'planned' => 'bg-gray-100 text-gray-700',
    'confirmed' => 'bg-blue-100 text-blue-700',
    'active' => 'bg-green-100 text-green-700',
    'extended' => 'bg-orange-100 text-orange-700',
    'terminated' => 'bg-red-100 text-red-700',
    'cancelled' => 'bg-gray-100 text-gray-500',
    'pending' => 'bg-yellow-100 text-yellow-700',
    'signed' => 'bg-green-100 text-green-700',
    'expired' => 'bg-red-100 text-red-700',
];
$color = $colors[$status] ?? 'bg-gray-100 text-gray-600';
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
    {{ __('common.status.' . $status) !== 'common.status.' . $status ? __('common.status.' . $status) : $status }}
</span>
