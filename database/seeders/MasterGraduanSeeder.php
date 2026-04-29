<?php

namespace Database\Seeders;

use App\Models\Talent;
use Illuminate\Database\Seeder;

class MasterGraduanSeeder extends Seeder
{
    public function run(): void
    {
        $rows = require database_path('seeders/data/master_graduan.php');

        foreach ($rows as $row) {
            Talent::updateOrCreate(
                ['id_graduan' => $row['id_graduan']],
                $row
            );
        }
    }
}
