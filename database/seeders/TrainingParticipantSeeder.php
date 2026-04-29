<?php

namespace Database\Seeders;

use App\Models\TrainingParticipant;
use App\Models\TrainingRecord;
use Illuminate\Database\Seeder;

class TrainingParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = ['Ahmad', 'Siti', 'Ibrahim', 'Nurul', 'Omar', 'Nadia', 'Abdul', 'Razak', 'Fatimah', 'Muhammad'];
        $lastNames = ['Ali', 'Rahman', 'Karim', 'Idris', 'Taib', 'Yusof', 'Hassan', 'Abdullah'];
        $mentorComments = [null, 'Good progress, needs more practice', 'Significant improvement', 'Active participation', 'Excellent teamwork'];

        $records = TrainingRecord::where('status', 'Selesai')->get();
        $recId = 1;

        foreach ($records as $tr) {
            $count = $tr->jumlah_hadir > 0 ? $tr->jumlah_hadir : rand(8, 15);

            for ($i = 0; $i < $count; $i++) {
                $gradId = 'P' . str_pad(rand(1, 200), 4, '0', STR_PAD_LEFT);
                $nama = $firstNames[array_rand($firstNames)] . ' bin/binti ' . $lastNames[array_rand($lastNames)];
                $pre = round(rand(45, 80) / 10, 1);
                $post = round(rand(70, 98) / 10, 1);
                $imp = $pre > 0 ? round((($post - $pre) / $pre) * 100, 1) : 0;

                TrainingParticipant::updateOrCreate(
                    ['id_record' => 'TPR' . str_pad($recId, 5, '0', STR_PAD_LEFT)],
                    [
                        'id_training' => $tr->id_training,
                        'id_graduan' => $gradId,
                        'nama_graduan' => $nama,
                        'status_kehadiran' => rand(0, 8) > 0 ? 'Hadir' : (rand(0, 1) ? 'Lewat' : 'Tidak Hadir'),
                        'pre_assessment_score' => $pre,
                        'post_assessment_score' => $post,
                        'improvement_pct' => $imp,
                        'certificate_issued' => rand(0, 4) > 0,
                        'feedback_submitted' => rand(0, 3) > 0,
                        'action_plan_submitted' => rand(0, 2) > 0,
                        'mentor_feedback' => $mentorComments[array_rand($mentorComments)],
                    ]
                );
                $recId++;
            }
        }
    }
}
