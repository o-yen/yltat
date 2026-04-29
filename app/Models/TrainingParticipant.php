<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingParticipant extends Model
{
    use HasFactory;

    protected $table = 'training_participants';

    protected $fillable = [
        'id_record', 'id_training', 'id_graduan', 'nama_graduan',
        'status_kehadiran',
        'pre_assessment_score', 'post_assessment_score', 'improvement_pct',
        'certificate_issued', 'feedback_submitted', 'action_plan_submitted',
        'mentor_feedback', 'catatan',
    ];

    protected $casts = [
        'pre_assessment_score' => 'decimal:2',
        'post_assessment_score' => 'decimal:2',
        'improvement_pct' => 'decimal:2',
        'certificate_issued' => 'boolean',
        'feedback_submitted' => 'boolean',
        'action_plan_submitted' => 'boolean',
    ];

    public function trainingRecord()
    {
        return $this->belongsTo(TrainingRecord::class, 'id_training', 'id_training');
    }

    public static function generateId(): string
    {
        $last = static::orderByDesc('id_record')->first();
        if ($last && preg_match('/TPR(\d+)/', $last->id_record, $m)) {
            $num = str_pad((int) $m[1] + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $num = '00001';
        }
        return "TPR{$num}";
    }
}
