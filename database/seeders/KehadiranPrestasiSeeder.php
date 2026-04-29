<?php

namespace Database\Seeders;

use App\Models\KehadiranPrestasi;
use App\Models\SyarikatPelaksana;
use App\Models\SyarikatPenempatan;
use Illuminate\Database\Seeder;

class KehadiranPrestasiSeeder extends Seeder
{
    public function run(): void
    {
        $pelaksanaIds = SyarikatPelaksana::pluck('id_pelaksana')->toArray();
        $penempatanIds = SyarikatPenempatan::pluck('id_syarikat')->toArray();

        $months = [
            'September 2025', 'Oktober 2025', 'November 2025',
            'Disember 2025', 'Januari 2026', 'Februari 2026',
        ];
        $years = [2025, 2025, 2025, 2025, 2026, 2026];

        $statuses = ['Dikemukakan', 'Lewat', 'Belum Dikemukakan'];
        $komens = [null, 'Cemerlang', 'Prestasi baik', 'Perlu penambahbaikan', 'Memuaskan'];

        $batch = [];
        // Generate ~80 records per month (480 total)
        for ($m = 0; $m < 6; $m++) {
            $graduanCount = $m < 3 ? 80 : 80; // consistent per month
            for ($g = 0; $g < $graduanCount; $g++) {
                $id = 'P' . str_pad(rand(1, 200), 4, '0', STR_PAD_LEFT);
                $hariBekerja = rand(20, 23);
                $hariHadir = rand(max(12, $hariBekerja - 8), $hariBekerja);
                $kehadiran = round($hariHadir / $hariBekerja, 2);

                $batch[] = [
                    'id_graduan' => $id,
                    'id_syarikat' => $penempatanIds[array_rand($penempatanIds)],
                    'id_pelaksana' => $pelaksanaIds[array_rand($pelaksanaIds)],
                    'bulan' => $months[$m],
                    'tahun' => $years[$m],
                    'kehadiran_pct' => $kehadiran,
                    'hari_hadir' => $hariHadir,
                    'hari_bekerja' => $hariBekerja,
                    'skor_prestasi' => rand(5, 10),
                    'komen_mentor' => $komens[array_rand($komens)],
                    'status_logbook' => $statuses[array_rand($statuses)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Use insertOrIgnore to skip duplicates on unique constraint
        foreach (array_chunk($batch, 100) as $chunk) {
            KehadiranPrestasi::insertOrIgnore($chunk);
        }
    }
}
