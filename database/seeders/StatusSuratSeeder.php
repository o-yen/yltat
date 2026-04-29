<?php

namespace Database\Seeders;

use App\Models\StatusSurat;
use App\Models\SyarikatPelaksana;
use Illuminate\Database\Seeder;

class StatusSuratSeeder extends Seeder
{
    public function run(): void
    {
        $pelaksanaIds = SyarikatPelaksana::pluck('id_pelaksana')->toArray();
        $jenisList = ['Surat Kuning', 'Surat Biru'];
        $statusList = ['Belum Mula', 'Draft', 'Semakan', 'Tandatangan', 'Hantar', 'Selesai'];
        $picNames = ['Ahmad Ismail', 'Zainab Mohamed', 'Ibrahim Ibrahim', 'Nurul Aziz',
                     'Fatimah Abdullah', 'Hidayah Hamid', 'Abdul Ismail', 'Muhammad Yusof',
                     'Siti Ismail', 'Ali Hassan'];
        $isuOptions = [null, null, null, 'Dokumen tidak lengkap', 'Menunggu tandatangan'];

        $batch = [];
        for ($i = 0; $i < 150; $i++) {
            $pelaksana = $pelaksanaIds[array_rand($pelaksanaIds)];
            $jenis = $jenisList[array_rand($jenisList)];
            $statusIdx = rand(0, 5);
            $status = $statusList[$statusIdx];
            $id_graduan = 'P' . str_pad(rand(1, 200), 4, '0', STR_PAD_LEFT);

            $startDate = date('Y-m-d', strtotime('2025-08-01 +' . rand(0, 150) . ' days'));

            $dates = [
                'tarikh_mula_proses' => $startDate,
                'tarikh_draft' => $statusIdx >= 1 ? date('Y-m-d', strtotime($startDate . ' +' . rand(2, 5) . ' days')) : null,
                'tarikh_semakan' => $statusIdx >= 2 ? date('Y-m-d', strtotime($startDate . ' +' . rand(5, 8) . ' days')) : null,
                'tarikh_tandatangan' => $statusIdx >= 3 ? date('Y-m-d', strtotime($startDate . ' +' . rand(7, 10) . ' days')) : null,
                'tarikh_hantar' => $statusIdx >= 4 ? date('Y-m-d', strtotime($startDate . ' +' . rand(9, 12) . ' days')) : null,
                'tarikh_siap' => $statusIdx >= 5 ? date('Y-m-d', strtotime($startDate . ' +' . rand(10, 14) . ' days')) : null,
            ];

            $batch[] = array_merge([
                'id_pelaksana' => $pelaksana,
                'jenis_surat' => $jenis,
                'id_graduan' => $id_graduan,
                'nama_graduan' => ['Ahmad', 'Siti', 'Ibrahim', 'Nurul', 'Omar', 'Nadia', 'Zainab', 'Fatimah'][rand(0, 7)] . ' bin ' . ['Ali', 'Rahman', 'Karim', 'Osman', 'Taib', 'Yusof', 'Idris', 'Ismail'][rand(0, 7)],
                'status_surat' => $status,
                'pic_responsible' => $picNames[array_rand($picNames)],
                'isu_halangan' => $statusIdx < 3 ? $isuOptions[array_rand($isuOptions)] : null,
                'catatan' => rand(0, 3) === 0 ? 'Diperlukan segera' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ], $dates);
        }

        foreach (array_chunk($batch, 50) as $chunk) {
            StatusSurat::insert($chunk);
        }
    }
}
