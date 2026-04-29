<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StatusSurat extends Model
{
    use HasFactory;

    protected $table = 'status_surat';

    protected $fillable = [
        'id_pelaksana', 'jenis_surat', 'id_graduan', 'nama_graduan',
        'status_surat', 'tarikh_mula_proses', 'tarikh_draft',
        'tarikh_semakan', 'tarikh_tandatangan', 'tarikh_hantar', 'tarikh_siap',
        'pic_responsible', 'isu_halangan', 'catatan',
        'file_attachment', 'file_name',
    ];

    protected $casts = [
        'tarikh_mula_proses' => 'date',
        'tarikh_draft' => 'date',
        'tarikh_semakan' => 'date',
        'tarikh_tandatangan' => 'date',
        'tarikh_hantar' => 'date',
        'tarikh_siap' => 'date',
    ];

    public const WORKFLOW = [
        'Belum Mula', 'Draft', 'Semakan', 'Tandatangan', 'Hantar', 'Selesai',
    ];

    public function syarikatPelaksana()
    {
        return $this->belongsTo(SyarikatPelaksana::class, 'id_pelaksana', 'id_pelaksana');
    }

    /**
     * Days in current workflow stage.
     */
    public function getDaysInCurrentStageAttribute(): int
    {
        $dateField = match ($this->status_surat) {
            'Draft' => $this->tarikh_draft,
            'Semakan' => $this->tarikh_semakan,
            'Tandatangan' => $this->tarikh_tandatangan,
            'Hantar' => $this->tarikh_hantar,
            'Selesai' => $this->tarikh_siap,
            default => $this->tarikh_mula_proses,
        };

        return $dateField ? Carbon::parse($dateField)->diffInDays(now()) : 0;
    }

    /**
     * SLA thresholds per stage transition.
     */
    public function getSlaStatusAttribute(): string
    {
        $sla = match ($this->status_surat) {
            'Belum Mula' => 5,
            'Draft' => 3,
            'Semakan' => 2,
            'Tandatangan' => 2,
            default => 0,
        };

        if ($sla === 0) return 'on_track';
        return $this->days_in_current_stage > $sla ? 'overdue' : 'on_track';
    }

    public function getWorkflowStepAttribute(): int
    {
        return array_search($this->status_surat, self::WORKFLOW) ?: 0;
    }
}
