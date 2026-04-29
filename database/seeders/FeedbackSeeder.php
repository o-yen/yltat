<?php

namespace Database\Seeders;

use App\Models\InternshipFeedback;
use App\Models\Placement;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $placements = Placement::with('talent', 'company')->get();

        $companyComments = [
            'Peserta menunjukkan prestasi yang sangat baik dalam tugasan harian.',
            'Menunjukkan inisiatif yang tinggi dan mudah bekerja dalam kumpulan.',
            'Prestasi memuaskan. Perlu tingkatkan kemahiran komunikasi.',
            'Sangat berdisiplin dan menepati masa. Aset yang baik untuk syarikat.',
            'Kemahiran teknikal yang baik tetapi perlu lebih proaktif.',
            'Peserta cepat belajar dan mampu menyesuaikan diri dengan persekitaran kerja.',
            'Menunjukkan peningkatan yang ketara sejak bulan pertama.',
            'Berupaya menyelesaikan masalah secara kreatif dan efisien.',
            'Perlu lebih fokus pada pengurusan masa dan keutamaan tugasan.',
            'Peserta mampu bekerja secara berdikari dengan pengawasan minimum.',
        ];

        $talentComments = [
            'Pengalaman yang sangat berharga. Saya banyak belajar tentang industri ini.',
            'Penyelia sangat membantu dan sentiasa memberi bimbingan yang baik.',
            'Persekitaran kerja yang positif dan menyokong pembelajaran.',
            'Mendapat pendedahan kepada pelbagai aspek kerja yang tidak dipelajari di universiti.',
            'Program ini membantu saya memahami hala tuju kerjaya saya.',
            'Latihan praktikal yang diberikan sangat relevan dengan bidang pengajian saya.',
            'Saya berterima kasih atas peluang ini dan ingin terus berkhidmat di syarikat ini.',
            'Cabaran kerja yang diberi membantu saya berkembang secara profesional.',
        ];

        $yltatComments = [
            'Peserta menunjukkan potensi yang tinggi untuk diserap ke dalam syarikat.',
            'Pemantauan berkala menunjukkan perkembangan positif peserta.',
            'Peserta memenuhi semua KPI yang ditetapkan untuk tempoh penilaian.',
            'Disyorkan untuk penyerapan. Prestasi keseluruhan cemerlang.',
            'Peserta aktif dalam aktiviti syarikat dan menunjukkan sikap positif.',
            'Perlu pemantauan lanjut untuk penambahbaikan kemahiran interpersonal.',
        ];

        foreach ($placements as $placement) {
            if (!$placement->start_date) {
                continue;
            }

            $startDate = Carbon::parse($placement->start_date);
            $isActive = in_array($placement->placement_status, ['active', 'confirmed']);
            $isCompleted = $placement->placement_status === 'completed';

            // Company feedback (month 2-3)
            $companyDate = $startDate->copy()->addMonths(rand(2, 3))->addDays(rand(0, 14));
            if ($companyDate->lte(now())) {
                InternshipFeedback::updateOrCreate(
                    [
                        'placement_id' => $placement->id,
                        'feedback_from' => 'company',
                    ],
                    [
                        'score_technical' => rand(3, 5),
                        'score_communication' => rand(3, 5),
                        'score_discipline' => rand(3, 5),
                        'score_problem_solving' => rand(2, 5),
                        'score_professionalism' => rand(3, 5),
                        'comments' => $companyComments[array_rand($companyComments)],
                        'submitted_at' => $companyDate,
                    ]
                );
            }

            // Talent feedback (month 3-4)
            $talentDate = $startDate->copy()->addMonths(rand(3, 4))->addDays(rand(0, 10));
            if ($talentDate->lte(now())) {
                InternshipFeedback::updateOrCreate(
                    [
                        'placement_id' => $placement->id,
                        'feedback_from' => 'talent',
                    ],
                    [
                        'score_technical' => rand(3, 5),
                        'score_communication' => rand(3, 5),
                        'score_discipline' => rand(4, 5),
                        'score_problem_solving' => rand(3, 5),
                        'score_professionalism' => rand(3, 5),
                        'comments' => $talentComments[array_rand($talentComments)],
                        'submitted_at' => $talentDate,
                    ]
                );
            }

            // YLTAT feedback (month 4-5, mainly for completed/active)
            if ($isActive || $isCompleted) {
                $yltatDate = $startDate->copy()->addMonths(rand(4, 5))->addDays(rand(0, 7));
                if ($yltatDate->lte(now())) {
                    InternshipFeedback::updateOrCreate(
                        [
                            'placement_id' => $placement->id,
                            'feedback_from' => 'yltat',
                        ],
                        [
                            'score_technical' => rand(3, 5),
                            'score_communication' => rand(3, 5),
                            'score_discipline' => rand(3, 5),
                            'score_problem_solving' => rand(3, 5),
                            'score_professionalism' => rand(3, 5),
                            'comments' => $yltatComments[array_rand($yltatComments)],
                            'submitted_at' => $yltatDate,
                        ]
                    );
                }
            }
        }
    }
}
