<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Placement;
use App\Models\Talent;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PlacementSeeder extends Seeder
{
    public function run(): void
    {
        $allowanceLookup = require database_path('seeders/data/elaun_by_graduan.php');

        $talents = Talent::query()
            ->whereNotNull('id_syarikat_penempatan')
            ->whereNotNull('tarikh_mula')
            ->whereNotNull('tarikh_tamat')
            ->get();

        foreach ($talents as $talent) {
            $company = Company::where('company_code', $talent->id_syarikat_penempatan)->first();

            if (! $company) {
                continue;
            }

            $startDate = Carbon::parse($talent->tarikh_mula);
            $endDate = Carbon::parse($talent->tarikh_tamat);
            $monthlyStipend = (float) ($allowanceLookup[$talent->id_graduan] ?? 0);

            Placement::updateOrCreate(
                [
                    'talent_id' => $talent->id,
                    'company_id' => $company->id,
                    'start_date' => $startDate->toDateString(),
                ],
                [
                    'batch_id' => null,
                    'department' => $talent->jawatan,
                    'supervisor_name' => $company->contact_person,
                    'supervisor_email' => $company->contact_email,
                    'end_date' => $endDate->toDateString(),
                    'duration_months' => max(1, $startDate->diffInMonths($endDate)),
                    'monthly_stipend' => $monthlyStipend,
                    'additional_cost' => 0,
                    'placement_status' => match ($talent->status_aktif) {
                        'Aktif' => 'active',
                        'Tamat' => 'completed',
                        'Berhenti Awal' => 'terminated',
                        default => 'planned',
                    },
                    'programme_type' => 'PROTEGE RTW',
                    'remarks' => collect([
                        $talent->kategori,
                        $talent->status_penyerapan_6bulan,
                    ])->filter()->implode(' • '),
                ]
            );
        }
    }
}
