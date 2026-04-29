<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipFeedback extends Model
{
    use HasFactory;

    protected $table = 'internship_feedback';

    protected $fillable = [
        'placement_id',
        'feedback_from',
        'score_technical',
        'score_communication',
        'score_discipline',
        'score_problem_solving',
        'score_professionalism',
        'comments',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function placement()
    {
        return $this->belongsTo(Placement::class);
    }

    public function getAverageScoreAttribute(): ?float
    {
        $scores = array_filter([
            $this->score_technical,
            $this->score_communication,
            $this->score_discipline,
            $this->score_problem_solving,
            $this->score_professionalism,
        ]);

        return count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : null;
    }
}
