<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingRecord extends Model
{
    use HasFactory;

    protected $table = 'training_records';

    protected $fillable = [
        'id_training', 'id_syarikat', 'nama_syarikat',
        'jenis_training', 'tajuk_training', 'sesi',
        'tarikh_training', 'durasi_jam', 'lokasi',
        'trainer_name', 'trainer_type',
        'jumlah_dijemput', 'jumlah_hadir', 'kadar_kehadiran_pct',
        'topik_covered',
        'pre_assessment_avg', 'post_assessment_avg', 'improvement_pct',
        'skor_kepuasan', 'budget_allocated', 'budget_spent',
        'status', 'catatan',
    ];

    protected $casts = [
        'tarikh_training' => 'date',
        'kadar_kehadiran_pct' => 'decimal:2',
        'pre_assessment_avg' => 'decimal:2',
        'post_assessment_avg' => 'decimal:2',
        'improvement_pct' => 'decimal:2',
        'skor_kepuasan' => 'decimal:2',
        'budget_allocated' => 'decimal:2',
        'budget_spent' => 'decimal:2',
    ];

    public function syarikatPenempatan()
    {
        return $this->belongsTo(SyarikatPenempatan::class, 'id_syarikat', 'id_syarikat');
    }

    public function participants()
    {
        return $this->hasMany(TrainingParticipant::class, 'id_training', 'id_training');
    }

    public static function generateId(): string
    {
        $last = static::orderByDesc('id_training')->first();
        if ($last && preg_match('/TRN(\d+)/', $last->id_training, $m)) {
            $num = str_pad((int) $m[1] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $num = '0001';
        }
        return "TRN{$num}";
    }
}
