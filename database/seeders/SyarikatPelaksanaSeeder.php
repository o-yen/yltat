<?php

namespace Database\Seeders;

use App\Models\SyarikatPelaksana;
use Illuminate\Database\Seeder;

class SyarikatPelaksanaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id_pelaksana' => 'SPANA_001',
                'nama_syarikat' => 'AIROD',
                'projek_kontrak' => 'Projek Pesawat MRO 2024-2026',
                'jumlah_kuota_obligasi' => 50,
                'kuota_diluluskan' => 55,
                'kuota_digunakan' => 52,
                'peruntukan_diluluskan' => 1188000,
                'peruntukan_diguna' => 904280,
                'baki_peruntukan' => 283720,
                'status_surat_kuning' => 'Dalam Proses',
                'status_surat_biru' => 'Siap',
                'pic_syarikat' => 'Yusof Ali',
                'email_pic' => 'pic@airod.com.my',
                'status_dana' => 'Perlu Perhatian',
                'tahap_pematuhan' => 'Perlu Penambahbaikan',
            ],
            [
                'id_pelaksana' => 'SPANA_002',
                'nama_syarikat' => 'ZETRO',
                'projek_kontrak' => 'Projek Sistem Pertahanan 2024-2027',
                'jumlah_kuota_obligasi' => 60,
                'kuota_diluluskan' => 65,
                'kuota_digunakan' => 61,
                'peruntukan_diluluskan' => 1404000,
                'peruntukan_diguna' => 860344,
                'baki_peruntukan' => 543656,
                'status_surat_kuning' => 'Belum Mula',
                'status_surat_biru' => 'Siap',
                'pic_syarikat' => 'Hidayah Idris',
                'email_pic' => 'pic@zetro.com.my',
                'status_dana' => 'Mencukupi',
                'tahap_pematuhan' => 'Baik',
            ],
            [
                'id_pelaksana' => 'SPANA_003',
                'nama_syarikat' => 'DEFTECH',
                'projek_kontrak' => 'Projek Kenderaan Perisai 2025-2027',
                'jumlah_kuota_obligasi' => 45,
                'kuota_diluluskan' => 50,
                'kuota_digunakan' => 45,
                'peruntukan_diluluskan' => 1080000,
                'peruntukan_diguna' => 810540,
                'baki_peruntukan' => 269460,
                'status_surat_kuning' => 'Belum Mula',
                'status_surat_biru' => 'Dalam Proses',
                'pic_syarikat' => 'Ahmad Rahman',
                'email_pic' => 'pic@deftech.com.my',
                'status_dana' => 'Mencukupi',
                'tahap_pematuhan' => 'Perlu Penambahbaikan',
            ],
            [
                'id_pelaksana' => 'SPANA_004',
                'nama_syarikat' => 'BOUSTEAD HEAVY',
                'projek_kontrak' => 'Projek Kapal Patrol 2024-2026',
                'jumlah_kuota_obligasi' => 40,
                'kuota_diluluskan' => 45,
                'kuota_digunakan' => 42,
                'peruntukan_diluluskan' => 972000,
                'peruntukan_diguna' => 653520,
                'baki_peruntukan' => 318480,
                'status_surat_kuning' => 'Siap',
                'status_surat_biru' => 'Dalam Proses',
                'pic_syarikat' => 'Ahmad Karim',
                'email_pic' => 'pic@bousteadheavy.com.my',
                'status_dana' => 'Kritikal',
                'tahap_pematuhan' => 'Sederhana',
            ],
        ];

        foreach ($data as $row) {
            SyarikatPelaksana::updateOrCreate(
                ['id_pelaksana' => $row['id_pelaksana']],
                $row
            );
        }
    }
}
