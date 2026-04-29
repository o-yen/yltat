<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\SyarikatPenempatan;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = SyarikatPenempatan::orderBy('id_syarikat')->get();

        foreach ($companies as $company) {
            Company::updateOrCreate(
                ['company_code' => $company->id_syarikat],
                [
                    'company_name' => $company->nama_syarikat,
                    'registration_no' => $company->id_syarikat,
                    'industry' => $company->sektor_industri,
                    'address' => null,
                    'contact_person' => $company->pic,
                    'contact_email' => $company->email_pic,
                    'contact_phone' => $company->no_telefon_pic,
                    'agreement_status' => 'signed',
                    'status' => 'active',
                    'notes' => $company->catatan,
                ]
            );
        }
    }
}
