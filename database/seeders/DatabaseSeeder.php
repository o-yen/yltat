<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $dumpPath = (string) env(
            'SQL_DUMP_SEEDER_PATH',
            database_path('seeders/data/original_dump.sql')
        );

        if (is_file($dumpPath)) {
            $this->call([
                SqlDumpRestoreSeeder::class,
            ]);

            return;
        }

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            SyarikatPelaksanaSeeder::class,
            SyarikatPenempatanSeeder::class,
            MasterGraduanSeeder::class,
            DimDateSeeder::class,
            KehadiranPrestasiSeeder::class,
            KewanganElaunSeeder::class,
            StatusSuratSeeder::class,
            LogbookUploadSeeder::class,
            IsuRisikoSeeder::class,
            TrainingRecordSeeder::class,
            TrainingParticipantSeeder::class,
            KpiDashboardSeeder::class,
            CompanySeeder::class,
            PlacementSeeder::class,
        ]);
    }
}
