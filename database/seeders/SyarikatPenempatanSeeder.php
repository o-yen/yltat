<?php

namespace Database\Seeders;

use App\Models\SyarikatPenempatan;
use Illuminate\Database\Seeder;

class SyarikatPenempatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id_syarikat' => 'SPTAN_001', 'nama_syarikat' => 'BOUSTEAD PLANTATIONS BERHAD',
                'jenis_syarikat' => 'Rakan Kolaborasi', 'sektor_industri' => 'Perladangan',
                'kuota_dipersetujui' => 26, 'jumlah_graduan_ditempatkan' => 25,
                'pic' => 'Zainab Ali', 'no_telefon_pic' => '03-92125519', 'email_pic' => 'pic@boustead.com.my',
                'laporan_bulanan' => 'Tertangguh', 'status_pematuhan' => 'Memuaskan',
                'catatan' => 'Perlu susulan laporan kehadiran',
                'soft_skills_sesi1_status' => 'Selesai', 'soft_skills_sesi1_tarikh' => '2025-09-17', 'soft_skills_sesi1_peserta' => 25,
                'soft_skills_sesi2_status' => 'Belum Mula', 'soft_skills_sesi2_tarikh' => null, 'soft_skills_sesi2_peserta' => 0,
                'training_compliance_pct' => 100, 'status_training' => 'Dalam Proses',
            ],
            [
                'id_syarikat' => 'SPTAN_002', 'nama_syarikat' => 'MYDIN MOHAMED HOLDINGS',
                'jenis_syarikat' => 'Rakan Kolaborasi', 'sektor_industri' => 'Runcit',
                'kuota_dipersetujui' => 15, 'jumlah_graduan_ditempatkan' => 13,
                'pic' => 'Ibrahim Ali', 'no_telefon_pic' => '03-34856599', 'email_pic' => 'pic@mydin.com.my',
                'laporan_bulanan' => 'Tertangguh', 'status_pematuhan' => 'Baik',
                'catatan' => null,
                'soft_skills_sesi1_status' => 'Dalam Perancangan', 'soft_skills_sesi1_tarikh' => '2026-03-22', 'soft_skills_sesi1_peserta' => 0,
                'soft_skills_sesi2_status' => 'Belum Mula', 'soft_skills_sesi2_tarikh' => null, 'soft_skills_sesi2_peserta' => 0,
                'training_compliance_pct' => 0, 'status_training' => 'Perlu Tindakan',
            ],
            [
                'id_syarikat' => 'SPTAN_003', 'nama_syarikat' => 'SAPURA ENERGY BERHAD',
                'jenis_syarikat' => 'Rakan Kolaborasi', 'sektor_industri' => 'Minyak & Gas',
                'kuota_dipersetujui' => 27, 'jumlah_graduan_ditempatkan' => 24,
                'pic' => 'Hidayah Ali', 'no_telefon_pic' => '03-78374938', 'email_pic' => 'pic@sapura.com.my',
                'laporan_bulanan' => 'Lengkap', 'status_pematuhan' => 'Perlu Penambahbaikan',
                'catatan' => 'Perlu susulan laporan kehadiran',
                'soft_skills_sesi1_status' => 'Selesai', 'soft_skills_sesi1_tarikh' => '2025-08-05', 'soft_skills_sesi1_peserta' => 22,
                'soft_skills_sesi2_status' => 'Selesai', 'soft_skills_sesi2_tarikh' => '2025-11-11', 'soft_skills_sesi2_peserta' => 22,
                'training_compliance_pct' => 91.7, 'status_training' => 'Cemerlang',
            ],
            [
                'id_syarikat' => 'SPTAN_004', 'nama_syarikat' => 'MALAYSIA AIRLINES BERHAD',
                'jenis_syarikat' => 'Rakan Kolaborasi', 'sektor_industri' => 'Penerbangan',
                'kuota_dipersetujui' => 25, 'jumlah_graduan_ditempatkan' => 22,
                'pic' => 'Mariam Ahmad', 'no_telefon_pic' => '03-43873270', 'email_pic' => 'pic@malaysia.com.my',
                'laporan_bulanan' => 'Lengkap', 'status_pematuhan' => 'Baik',
                'catatan' => 'Perlu susulan laporan kehadiran',
                'soft_skills_sesi1_status' => 'Dalam Perancangan', 'soft_skills_sesi1_tarikh' => '2026-03-27', 'soft_skills_sesi1_peserta' => 0,
                'soft_skills_sesi2_status' => 'Belum Mula', 'soft_skills_sesi2_tarikh' => null, 'soft_skills_sesi2_peserta' => 0,
                'training_compliance_pct' => 0, 'status_training' => 'Perlu Tindakan',
            ],
            [
                'id_syarikat' => 'SPTAN_005', 'nama_syarikat' => 'PROTON HOLDINGS BERHAD',
                'jenis_syarikat' => 'Rakan Kolaborasi', 'sektor_industri' => 'Automotif',
                'kuota_dipersetujui' => 24, 'jumlah_graduan_ditempatkan' => 22,
                'pic' => 'Ali Rashid', 'no_telefon_pic' => '03-33783589', 'email_pic' => 'pic@proton.com.my',
                'laporan_bulanan' => 'Lengkap', 'status_pematuhan' => 'Cemerlang',
                'catatan' => null,
                'soft_skills_sesi1_status' => 'Belum Mula', 'soft_skills_sesi1_tarikh' => null, 'soft_skills_sesi1_peserta' => 0,
                'soft_skills_sesi2_status' => 'Belum Mula', 'soft_skills_sesi2_tarikh' => null, 'soft_skills_sesi2_peserta' => 0,
                'training_compliance_pct' => 0, 'status_training' => 'Perlu Tindakan',
            ],
            [
                'id_syarikat' => 'SPTAN_006', 'nama_syarikat' => 'PETRONAS CHEMICALS',
                'jenis_syarikat' => 'Rakan Kolaborasi', 'sektor_industri' => 'Kimia',
                'kuota_dipersetujui' => 19, 'jumlah_graduan_ditempatkan' => 11,
                'pic' => 'Omar Rashid', 'no_telefon_pic' => '03-77375970', 'email_pic' => 'pic@petronas.com.my',
                'laporan_bulanan' => 'Lengkap', 'status_pematuhan' => 'Memuaskan',
                'catatan' => null,
                'soft_skills_sesi1_status' => 'Selesai', 'soft_skills_sesi1_tarikh' => '2025-08-13', 'soft_skills_sesi1_peserta' => 11,
                'soft_skills_sesi2_status' => 'Selesai', 'soft_skills_sesi2_tarikh' => '2025-11-12', 'soft_skills_sesi2_peserta' => 10,
                'training_compliance_pct' => 95.5, 'status_training' => 'Cemerlang',
            ],
            [
                'id_syarikat' => 'SPTAN_007', 'nama_syarikat' => 'SIME DARBY PROPERTY',
                'jenis_syarikat' => 'Rakan Kolaborasi', 'sektor_industri' => 'Hartanah',
                'kuota_dipersetujui' => 21, 'jumlah_graduan_ditempatkan' => 13,
                'pic' => 'Ali Ismail', 'no_telefon_pic' => '03-14295372', 'email_pic' => 'pic@sime.com.my',
                'laporan_bulanan' => 'Lengkap', 'status_pematuhan' => 'Baik',
                'catatan' => null,
                'soft_skills_sesi1_status' => 'Selesai', 'soft_skills_sesi1_tarikh' => '2025-08-21', 'soft_skills_sesi1_peserta' => 11,
                'soft_skills_sesi2_status' => 'Selesai', 'soft_skills_sesi2_tarikh' => '2025-11-29', 'soft_skills_sesi2_peserta' => 10,
                'training_compliance_pct' => 80.8, 'status_training' => 'Baik',
            ],
            [
                'id_syarikat' => 'SPTAN_008', 'nama_syarikat' => 'TENAGA NASIONAL BERHAD',
                'jenis_syarikat' => 'Rakan Kolaborasi', 'sektor_industri' => 'Utiliti',
                'kuota_dipersetujui' => 22, 'jumlah_graduan_ditempatkan' => 13,
                'pic' => 'Hafizah Ibrahim', 'no_telefon_pic' => '03-88083196', 'email_pic' => 'pic@tenaga.com.my',
                'laporan_bulanan' => 'Lengkap', 'status_pematuhan' => 'Perlu Penambahbaikan',
                'catatan' => null,
                'soft_skills_sesi1_status' => 'Selesai', 'soft_skills_sesi1_tarikh' => '2025-09-24', 'soft_skills_sesi1_peserta' => 13,
                'soft_skills_sesi2_status' => 'Selesai', 'soft_skills_sesi2_tarikh' => '2025-12-15', 'soft_skills_sesi2_peserta' => 13,
                'training_compliance_pct' => 100, 'status_training' => 'Cemerlang',
            ],
            [
                'id_syarikat' => 'SPTAN_009', 'nama_syarikat' => 'TELEKOM MALAYSIA',
                'jenis_syarikat' => 'Rakan Kolaborasi', 'sektor_industri' => 'Telekomunikasi',
                'kuota_dipersetujui' => 26, 'jumlah_graduan_ditempatkan' => 17,
                'pic' => 'Yusof Latif', 'no_telefon_pic' => '03-87633253', 'email_pic' => 'pic@telekom.com.my',
                'laporan_bulanan' => 'Lengkap', 'status_pematuhan' => 'Baik',
                'catatan' => null,
                'soft_skills_sesi1_status' => 'Selesai', 'soft_skills_sesi1_tarikh' => '2025-10-20', 'soft_skills_sesi1_peserta' => 17,
                'soft_skills_sesi2_status' => 'Selesai', 'soft_skills_sesi2_tarikh' => '2026-01-24', 'soft_skills_sesi2_peserta' => 17,
                'training_compliance_pct' => 100, 'status_training' => 'Cemerlang',
            ],
            [
                'id_syarikat' => 'SPTAN_010', 'nama_syarikat' => 'CIMB BANK BERHAD',
                'jenis_syarikat' => 'Rakan Kolaborasi', 'sektor_industri' => 'Kewangan',
                'kuota_dipersetujui' => 15, 'jumlah_graduan_ditempatkan' => 11,
                'pic' => 'Nadia Ali', 'no_telefon_pic' => '03-43234351', 'email_pic' => 'pic@cimb.com.my',
                'laporan_bulanan' => 'Lengkap', 'status_pematuhan' => 'Perlu Penambahbaikan',
                'catatan' => 'Perlu susulan laporan kehadiran',
                'soft_skills_sesi1_status' => 'Dalam Perancangan', 'soft_skills_sesi1_tarikh' => '2026-03-22', 'soft_skills_sesi1_peserta' => 0,
                'soft_skills_sesi2_status' => 'Belum Mula', 'soft_skills_sesi2_tarikh' => null, 'soft_skills_sesi2_peserta' => 0,
                'training_compliance_pct' => 0, 'status_training' => 'Perlu Tindakan',
            ],
        ];

        foreach ($data as $row) {
            SyarikatPenempatan::updateOrCreate(
                ['id_syarikat' => $row['id_syarikat']],
                $row
            );
        }
    }
}
