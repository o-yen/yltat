<?php

namespace Database\Seeders;

use App\Models\KewanganElaun;
use App\Models\SyarikatPelaksana;
use Illuminate\Database\Seeder;

class KewanganElaunSeeder extends Seeder
{
    public function run(): void
    {
        $pelaksanaIds = SyarikatPelaksana::pluck('id_pelaksana')->toArray();

        $months = [
            ['bulan' => 'September 2025', 'tahun' => 2025, 'start' => '2025-09-01', 'end' => '2025-09-30', 'days' => 30, 'jangka' => '2025-10-05'],
            ['bulan' => 'Oktober 2025', 'tahun' => 2025, 'start' => '2025-10-01', 'end' => '2025-10-31', 'days' => 31, 'jangka' => '2025-11-05'],
            ['bulan' => 'November 2025', 'tahun' => 2025, 'start' => '2025-11-01', 'end' => '2025-11-30', 'days' => 30, 'jangka' => '2025-12-05'],
            ['bulan' => 'Disember 2025', 'tahun' => 2025, 'start' => '2025-12-01', 'end' => '2025-12-31', 'days' => 31, 'jangka' => '2026-01-05'],
            ['bulan' => 'Januari 2026', 'tahun' => 2026, 'start' => '2026-01-01', 'end' => '2026-01-31', 'days' => 31, 'jangka' => '2026-02-05'],
            ['bulan' => 'Februari 2026', 'tahun' => 2026, 'start' => '2026-02-01', 'end' => '2026-02-28', 'days' => 28, 'jangka' => '2026-03-05'],
        ];

        $elaunOptions = [1500, 1600, 1700, 1800, 2000];
        $statusOptions = ['Selesai', 'Selesai', 'Selesai', 'Selesai', 'Lewat', 'Dalam Proses'];
        $catatanOptions = [null, null, null, 'Kelewatan proses dokumen', 'Menunggu kelulusan'];

        $batch = [];
        foreach ($months as $m) {
            for ($g = 0; $g < 80; $g++) {
                $id = 'P' . str_pad(rand(1, 200), 4, '0', STR_PAD_LEFT);
                $hariBekerja = rand(18, 23);
                $elaunPenuh = $elaunOptions[array_rand($elaunOptions)];
                $elaunProrate = round($elaunPenuh * ($hariBekerja / $m['days']), 2);
                $status = $statusOptions[array_rand($statusOptions)];

                $hariLewat = 0;
                $tarikhBayar = null;
                if ($status === 'Selesai') {
                    $tarikhBayar = date('Y-m-d', strtotime($m['jangka'] . ' -' . rand(0, 3) . ' days'));
                } elseif ($status === 'Lewat') {
                    $lewat = rand(3, 15);
                    $tarikhBayar = date('Y-m-d', strtotime($m['jangka'] . ' +' . $lewat . ' days'));
                    $hariLewat = $lewat;
                }

                $batch[] = [
                    'id_graduan' => $id,
                    'id_pelaksana' => $pelaksanaIds[array_rand($pelaksanaIds)],
                    'bulan' => $m['bulan'],
                    'tahun' => $m['tahun'],
                    'tarikh_mula_kerja' => $m['start'],
                    'tarikh_akhir_kerja' => $m['end'],
                    'hari_bekerja_sebenar' => $hariBekerja,
                    'hari_dalam_bulan' => $m['days'],
                    'elaun_penuh' => $elaunPenuh,
                    'elaun_prorate' => $elaunProrate,
                    'status_bayaran' => $status,
                    'tarikh_bayar' => $tarikhBayar,
                    'tarikh_jangka_bayar' => $m['jangka'],
                    'hari_lewat' => $hariLewat,
                    'catatan' => $catatanOptions[array_rand($catatanOptions)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach (array_chunk($batch, 100) as $chunk) {
            KewanganElaun::insertOrIgnore($chunk);
        }
    }
}
