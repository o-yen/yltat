<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KehadiranPrestasi extends Model
{
    use HasFactory;

    protected $table = 'kehadiran_prestasi';

    protected $fillable = [
        'id_graduan', 'id_syarikat', 'id_pelaksana',
        'bulan', 'tahun', 'kehadiran_pct', 'hari_hadir', 'hari_bekerja',
        'skor_prestasi', 'komen_mentor', 'status_logbook',
    ];

    protected $casts = [
        'kehadiran_pct' => 'decimal:2',
        'hari_hadir' => 'integer',
        'hari_bekerja' => 'integer',
        'skor_prestasi' => 'integer',
        'tahun' => 'integer',
    ];

    public function graduan()
    {
        return $this->belongsTo(Talent::class, 'id_graduan', 'talent_code');
    }

    public function syarikatPenempatan()
    {
        return $this->belongsTo(SyarikatPenempatan::class, 'id_syarikat', 'id_syarikat');
    }

    public function syarikatPelaksana()
    {
        return $this->belongsTo(SyarikatPelaksana::class, 'id_pelaksana', 'id_pelaksana');
    }

    public function getKehadiranLevelAttribute(): string
    {
        if ($this->kehadiran_pct >= 0.85) return 'Baik';
        if ($this->kehadiran_pct >= 0.75) return 'Sederhana';
        return 'Rendah';
    }

    public function getPrestasiLevelAttribute(): string
    {
        if ($this->skor_prestasi >= 8) return 'Cemerlang';
        if ($this->skor_prestasi >= 6) return 'Baik';
        return 'Perlu Perhatian';
    }
}
