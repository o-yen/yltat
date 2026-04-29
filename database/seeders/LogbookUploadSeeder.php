<?php

namespace Database\Seeders;

use App\Models\LogbookUpload;
use App\Models\SyarikatPenempatan;
use Illuminate\Database\Seeder;

class LogbookUploadSeeder extends Seeder
{
    public function run(): void
    {
        $companies = SyarikatPenempatan::pluck('nama_syarikat', 'id_syarikat')->toArray();
        $companyIds = array_keys($companies);

        $months = [
            'September 2025', 'Oktober 2025', 'November 2025',
            'Disember 2025', 'Januari 2026', 'Februari 2026',
        ];
        $years = [2025, 2025, 2025, 2025, 2026, 2026];

        $statusLogbook = ['Dikemukakan', 'Dikemukakan', 'Dikemukakan', 'Lewat', 'Belum Dikemukakan', 'Dalam Semakan'];
        $statusSemakan = ['Lulus', 'Lulus', 'Dalam Proses', 'Perlu Semakan Semula', 'Belum Disemak'];
        $komenOptions = [null, 'Logbook lengkap', 'Perlu tambah butiran', 'Baik'];
        $mentorNames = ['Ahmad Ahmad', 'Siti Ibrahim', 'Hassan Latif', 'Muhammad Ibrahim',
                        'Omar Hassan', 'Fatimah Latif', 'Muhammad Ismail', 'Zainab Mohamed',
                        'Fatimah Hamid', 'Muhammad Abdullah'];
        $firstNames = ['Ahmad', 'Siti', 'Ibrahim', 'Nurul', 'Omar', 'Nadia', 'Abdul', 'Razak', 'Fatimah', 'Muhammad', 'Aminah', 'Rozita', 'Ismail'];
        $lastNames = ['Ali', 'Rahman', 'Karim', 'Idris', 'Taib', 'Yusof', 'Hassan', 'Abdullah', 'Mohamed', 'Aziz', 'Majid', 'Osman'];

        $batch = [];
        for ($m = 0; $m < 6; $m++) {
            for ($g = 0; $g < 70; $g++) {
                $id = 'P' . str_pad(rand(1, 200), 4, '0', STR_PAD_LEFT);
                $companyId = $companyIds[array_rand($companyIds)];
                $stLogbook = $statusLogbook[array_rand($statusLogbook)];
                $nama = $firstNames[array_rand($firstNames)] . ' bin ' . $lastNames[array_rand($lastNames)];

                $tarikhUpload = null;
                $link = null;
                $stSemakan = 'Belum Disemak';
                $komen = null;
                $tarikhSemakan = null;

                if ($stLogbook !== 'Belum Dikemukakan') {
                    $day = rand(5, 28);
                    $monthNum = ($m < 4) ? (9 + $m) : ($m - 3);
                    $yearNum = $years[$m];
                    $tarikhUpload = sprintf('%04d-%02d-%02d', $yearNum, $monthNum, min($day, 28));
                    $link = "https://drive.google.com/logbook_{$id}_{$months[$m]}";
                    $stSemakan = $statusSemakan[array_rand($statusSemakan)];
                    if ($stSemakan !== 'Belum Disemak') {
                        $komen = $komenOptions[array_rand($komenOptions)];
                        $tarikhSemakan = date('Y-m-d', strtotime($tarikhUpload . ' +' . rand(1, 5) . ' days'));
                    }
                }

                $batch[] = [
                    'id_graduan' => $id,
                    'nama_graduan' => $nama,
                    'id_syarikat' => $companyId,
                    'nama_syarikat' => $companies[$companyId],
                    'bulan' => $months[$m],
                    'tahun' => $years[$m],
                    'status_logbook' => $stLogbook,
                    'tarikh_upload' => $tarikhUpload,
                    'link_file_logbook' => $link,
                    'status_semakan' => $stSemakan,
                    'komen_mentor' => $komen,
                    'tarikh_semakan' => $tarikhSemakan,
                    'nama_mentor' => $mentorNames[array_rand($mentorNames)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach (array_chunk($batch, 100) as $chunk) {
            LogbookUpload::insertOrIgnore($chunk);
        }
    }
}
