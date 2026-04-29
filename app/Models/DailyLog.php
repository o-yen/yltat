<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyLog extends Model
{
    use HasFactory;

    protected $table = 'daily_logs';

    protected $fillable = [
        'talent_id',
        'placement_id',
        'log_date',
        'activities',
        'challenges',
        'learnings',
        'mood',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_remarks',
    ];

    protected $casts = [
        'log_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function talent()
    {
        return $this->belongsTo(Talent::class);
    }

    public function placement()
    {
        return $this->belongsTo(Placement::class);
    }

    public function getMoodLabelAttribute(): string
    {
        return match($this->mood) {
            'great'     => __('talent.mood_with_emoji.great'),
            'good'      => __('talent.mood_with_emoji.good'),
            'neutral'   => __('talent.mood_with_emoji.neutral'),
            'tired'     => __('talent.mood_with_emoji.tired'),
            'difficult' => __('talent.mood_with_emoji.difficult'),
            default     => $this->mood,
        };
    }

    public function getMoodColorAttribute(): string
    {
        return match($this->mood) {
            'great'     => 'green',
            'good'      => 'blue',
            'neutral'   => 'gray',
            'tired'     => 'yellow',
            'difficult' => 'red',
            default     => 'gray',
        };
    }
}
