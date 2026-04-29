<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyarikatPelaksana extends Model
{
    use HasFactory;

    protected $table = 'syarikat_pelaksana';
    protected $primaryKey = 'id_pelaksana';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pelaksana',
        'nama_syarikat',
        'projek_kontrak',
        'jumlah_kuota_obligasi',
        'kuota_diluluskan',
        'kuota_digunakan',
        'peruntukan_diluluskan',
        'peruntukan_diguna',
        'baki_peruntukan',
        'status_surat_kuning',
        'status_surat_biru',
        'pic_syarikat',
        'email_pic',
        'status_dana',
        'tahap_pematuhan',
    ];

    protected $casts = [
        'peruntukan_diluluskan' => 'decimal:2',
        'peruntukan_diguna' => 'decimal:2',
        'baki_peruntukan' => 'decimal:2',
        'jumlah_kuota_obligasi' => 'integer',
        'kuota_diluluskan' => 'integer',
        'kuota_digunakan' => 'integer',
    ];

    /**
     * Graduan (Talents) under this pelaksana.
     */
    public function graduan()
    {
        return $this->hasMany(Talent::class, 'id_pelaksana', 'id_pelaksana');
    }

    /**
     * Users linked to this pelaksana.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'id_pelaksana', 'id_pelaksana');
    }

    /**
     * Calculate baki peruntukan.
     */
    public function getCalculatedBakiAttribute(): float
    {
        return $this->peruntukan_diluluskan - $this->peruntukan_diguna;
    }

    /**
     * Dana status indicator based on baki percentage.
     * Green: >20%, Yellow: 10-20%, Red: <10%
     */
    public function getCalculatedStatusDanaAttribute(): string
    {
        if ($this->peruntukan_diluluskan <= 0) {
            return 'Kritikal';
        }

        $bakiPct = ($this->baki_peruntukan / $this->peruntukan_diluluskan) * 100;

        if ($bakiPct > 20) {
            return 'Mencukupi';
        } elseif ($bakiPct >= 10) {
            return 'Perlu Perhatian';
        } else {
            return 'Kritikal';
        }
    }

    /**
     * Dana status color for UI.
     */
    public function getDanaColorAttribute(): string
    {
        return match ($this->status_dana) {
            'Mencukupi' => 'green',
            'Perlu Perhatian' => 'yellow',
            'Kritikal' => 'red',
            default => 'gray',
        };
    }

    /**
     * Generate next ID.
     */
    public static function generateId(): string
    {
        $last = static::orderByDesc('id_pelaksana')->first();

        if ($last && preg_match('/SPANA_(\d+)/', $last->id_pelaksana, $matches)) {
            $newNum = str_pad((int) $matches[1] + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNum = '001';
        }

        return "SPANA_{$newNum}";
    }
}
