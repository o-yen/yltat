<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KewanganElaun extends Model
{
    use HasFactory;

    protected $table = 'kewangan_elaun';

    protected $fillable = [
        'id_graduan', 'id_pelaksana', 'bulan', 'tahun',
        'tarikh_mula_kerja', 'tarikh_akhir_kerja',
        'hari_bekerja_sebenar', 'hari_dalam_bulan',
        'elaun_penuh', 'elaun_prorate',
        'status_bayaran', 'tarikh_bayar', 'tarikh_jangka_bayar',
        'hari_lewat', 'catatan',
    ];

    protected $casts = [
        'tarikh_mula_kerja' => 'date',
        'tarikh_akhir_kerja' => 'date',
        'tarikh_bayar' => 'date',
        'tarikh_jangka_bayar' => 'date',
        'elaun_penuh' => 'decimal:2',
        'elaun_prorate' => 'decimal:2',
        'hari_bekerja_sebenar' => 'integer',
        'hari_dalam_bulan' => 'integer',
        'hari_lewat' => 'integer',
        'tahun' => 'integer',
    ];

    public function graduan()
    {
        return $this->belongsTo(Talent::class, 'id_graduan', 'talent_code');
    }

    public function syarikatPelaksana()
    {
        return $this->belongsTo(SyarikatPelaksana::class, 'id_pelaksana', 'id_pelaksana');
    }

    /**
     * Auto-calculate pro-rate allowance.
     */
    public function calculateProRate(): float
    {
        if ($this->hari_dalam_bulan <= 0) return 0;
        return $this->elaun_penuh * ($this->hari_bekerja_sebenar / $this->hari_dalam_bulan);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->hari_lewat > 7;
    }
}
