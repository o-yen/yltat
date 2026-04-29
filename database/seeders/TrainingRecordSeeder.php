<?php

namespace Database\Seeders;

use App\Models\TrainingRecord;
use App\Models\SyarikatPenempatan;
use Illuminate\Database\Seeder;

class TrainingRecordSeeder extends Seeder
{
    public function run(): void
    {
        $companies = SyarikatPenempatan::pluck('nama_syarikat', 'id_syarikat')->toArray();
        $trainerNames = ['Zainab Ahmad', 'Aminah Ali', 'Hassan Ibrahim', 'Omar Rashid', 'Fatimah Latif', 'Ali Karim'];
        $topik = 'Professional Communication, Presentation Skills, Time Management, Teamwork Basics';

        $id = 1;
        foreach ($companies as $compId => $compName) {
            foreach (['Session 1', 'Session 2'] as $sIdx => $sesi) {
                $baseDate = $sIdx === 0
                    ? date('Y-m-d', strtotime('2025-08-01 +' . rand(0, 60) . ' days'))
                    : date('Y-m-d', strtotime('2025-11-01 +' . rand(0, 60) . ' days'));

                $dijemput = rand(10, 25);
                $hadir = rand(max(5, $dijemput - 5), $dijemput);
                $preAvg = round(rand(50, 70) / 10, 2);
                $postAvg = round(rand(75, 95) / 10, 2);
                $improvement = $preAvg > 0 ? round((($postAvg - $preAvg) / $preAvg) * 100, 2) : 0;
                $budgetAlloc = rand(15, 40) * 100;
                $budgetSpent = round($budgetAlloc * (rand(85, 100) / 100), 2);

                $status = $sIdx === 0 ? 'Selesai' : (rand(0, 2) > 0 ? 'Selesai' : 'Dirancang');

                TrainingRecord::updateOrCreate(
                    ['id_training' => 'TRN' . str_pad($id, 4, '0', STR_PAD_LEFT)],
                    [
                        'id_syarikat' => $compId,
                        'nama_syarikat' => $compName,
                        'jenis_training' => 'Soft Skills',
                        'tajuk_training' => "Soft Skills Development - {$sesi}",
                        'sesi' => $sesi,
                        'tarikh_training' => $baseDate,
                        'durasi_jam' => 8,
                        'lokasi' => 'Training Room / External Venue',
                        'trainer_name' => $trainerNames[array_rand($trainerNames)],
                        'trainer_type' => rand(0, 1) ? 'Internal' : 'External',
                        'jumlah_dijemput' => $dijemput,
                        'jumlah_hadir' => $status === 'Selesai' ? $hadir : 0,
                        'kadar_kehadiran_pct' => $status === 'Selesai' ? round(($hadir / $dijemput) * 100, 2) : 0,
                        'topik_covered' => $topik,
                        'pre_assessment_avg' => $status === 'Selesai' ? $preAvg : 0,
                        'post_assessment_avg' => $status === 'Selesai' ? $postAvg : 0,
                        'improvement_pct' => $status === 'Selesai' ? $improvement : 0,
                        'skor_kepuasan' => $status === 'Selesai' ? round(rand(75, 95) / 10, 1) : 0,
                        'budget_allocated' => $budgetAlloc,
                        'budget_spent' => $status === 'Selesai' ? $budgetSpent : 0,
                        'status' => $status,
                    ]
                );
                $id++;
            }
        }
    }
}
