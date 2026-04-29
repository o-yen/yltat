@extends('admin.system-guide.howto._layout')

@section('howto-content')

{{-- Section 1 --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">1</span>
        {{ __('howto.kpi_explained_step1_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.kpi_explained_step1_desc') }}</p>
    </div>
</section>

{{-- Section 2 --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">2</span>
        {{ __('howto.kpi_explained_step2_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.kpi_explained_step2_desc') }}</p>
    </div>
</section>

{{-- KPI Table --}}
<div class="overflow-x-auto">
    <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('howto.kpi_explained_th_indicator') }}</th>
                <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('howto.kpi_explained_th_formula') }}</th>
                <th class="text-left px-4 py-2 font-semibold text-gray-600">{{ __('howto.kpi_explained_th_target') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @for($i = 1; $i <= 8; $i++)
                <tr>
                    <td class="px-4 py-2 font-medium">{{ __("howto.kpi_explained_kpi{$i}_name") }}</td>
                    <td class="px-4 py-2 font-mono text-xs">{{ __("howto.kpi_explained_kpi{$i}_formula") }}</td>
                    <td class="px-4 py-2">{{ __("howto.kpi_explained_kpi{$i}_target") }}</td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>

{{-- Section 3 --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">3</span>
        {{ __('howto.kpi_explained_step3_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.kpi_explained_step3_desc') }}</p>
    </div>
</section>

{{-- Section 4 --}}
<section>
    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">4</span>
        {{ __('howto.kpi_explained_step4_title') }}
    </h2>
    <div class="bg-gray-50 rounded-lg p-4">
        <p>{{ __('howto.kpi_explained_step4_desc') }}</p>
    </div>
</section>

{{-- Info --}}
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
    <p class="text-blue-800">{{ __('howto.kpi_explained_info') }}</p>
</div>

@endsection
