@extends('layouts.admin')

@section('title', 'Applicant Requests')
@section('page-title', 'Applicant Requests')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">Pending</div>
            <div class="mt-2 text-3xl font-bold text-amber-600">{{ $counts['pending'] }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">Approved</div>
            <div class="mt-2 text-3xl font-bold text-emerald-600">{{ $counts['approved'] }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">Rejected</div>
            <div class="mt-2 text-3xl font-bold text-red-600">{{ $counts['rejected'] }}</div>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search applicant, graduate ID, email, implementing company..."
                   class="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-[#274670] focus:outline-none">
            <select name="status" class="rounded-xl border border-slate-200 px-4 py-3 text-sm">
                <option value="">All Statuses</option>
                @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-[#274670] px-5 py-3 text-sm font-semibold text-white">Filter</button>
        </div>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Applicant</th>
                        <th class="px-4 py-3 text-left">Requesting Implementation Company</th>
                        <th class="px-4 py-3 text-left">Placement Company</th>
                        <th class="px-4 py-3 text-left">Requested By</th>
                        <th class="px-4 py-3 text-left">Requested At</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $item)
                        <tr>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-slate-900">{{ $item->talent?->full_name ?? '-' }}</div>
                                <div class="text-slate-500">{{ $item->talent?->id_graduan ?? '-' }} • {{ $item->talent?->email ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-medium text-slate-800">{{ $item->implementingCompany?->nama_syarikat ?? '-' }}</div>
                                <div class="text-slate-500">{{ $item->implementingCompany?->id_pelaksana ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-medium text-slate-800">{{ $item->placementCompany?->nama_syarikat ?? 'Not assigned yet' }}</div>
                                <div class="text-slate-500">{{ $item->placementCompany?->id_syarikat ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-medium text-slate-800">{{ $item->requestedBy?->full_name ?? '-' }}</div>
                                <div class="text-slate-500">{{ $item->requestedBy?->email ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-slate-600">{{ optional($item->created_at)->format('d M Y H:i') }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $item->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $item->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $item->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @if($canReview && $item->status === 'pending')
                                        <form method="POST" action="{{ route('admin.applicant-requests.approve', $item) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.applicant-requests.reject', $item) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white">Reject</button>
                                        </form>
                                    @elseif($item->status !== 'pending')
                                        <span class="text-xs text-slate-500">Reviewed</span>
                                    @else
                                        <span class="text-xs text-slate-500">Awaiting Admin / PMO</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">No applicant requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $requests->links() }}
</div>
@endsection
