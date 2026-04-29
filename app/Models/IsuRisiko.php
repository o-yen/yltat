<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsuRisiko extends Model
{
    use HasFactory;

    protected $table = 'isu_risiko';

    protected $fillable = [
        'id_isu', 'tarikh_isu', 'id_pelaksana', 'id_syarikat',
        'kategori_isu', 'butiran_isu', 'tahap_risiko', 'status',
        'pic', 'tindakan_diambil', 'tarikh_tindakan', 'tarikh_tutup', 'catatan',
    ];

    protected $casts = [
        'tarikh_isu' => 'date',
        'tarikh_tindakan' => 'date',
        'tarikh_tutup' => 'date',
    ];

    public function syarikatPelaksana()
    {
        return $this->belongsTo(SyarikatPelaksana::class, 'id_pelaksana', 'id_pelaksana');
    }

    public function syarikatPenempatan()
    {
        return $this->belongsTo(SyarikatPenempatan::class, 'id_syarikat', 'id_syarikat');
    }

    public function getRisikoColorAttribute(): string
    {
        return match ($this->tahap_risiko) {
            'Kritikal' => 'red',
            'Tinggi' => 'orange',
            'Sederhana' => 'yellow',
            'Rendah' => 'green',
            default => 'gray',
        };
    }

    public static function generateId(): string
    {
        $last = static::orderByDesc('id_isu')->first();
        if ($last && preg_match('/ISU(\d+)/', $last->id_isu, $m)) {
            $num = str_pad((int) $m[1] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $num = '0001';
        }
        return "ISU{$num}";
    }
}
