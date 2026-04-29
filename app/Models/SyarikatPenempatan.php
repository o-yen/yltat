<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyarikatPenempatan extends Model
{
    use HasFactory;

    protected $table = 'syarikat_penempatan';
    protected $primaryKey = 'id_syarikat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_syarikat',
        'nama_syarikat',
        'jenis_syarikat',
        'sektor_industri',
        'kuota_dipersetujui',
        'jumlah_graduan_ditempatkan',
        'pic',
        'no_telefon_pic',
        'email_pic',
        'laporan_bulanan',
        'status_pematuhan',
        'catatan',
        'soft_skills_sesi1_status',
        'soft_skills_sesi1_tarikh',
        'soft_skills_sesi1_peserta',
        'soft_skills_sesi2_status',
        'soft_skills_sesi2_tarikh',
        'soft_skills_sesi2_peserta',
        'training_compliance_pct',
        'status_training',
    ];

    protected $casts = [
        'soft_skills_sesi1_tarikh' => 'date',
        'soft_skills_sesi2_tarikh' => 'date',
        'training_compliance_pct' => 'decimal:2',
        'kuota_dipersetujui' => 'integer',
        'jumlah_graduan_ditempatkan' => 'integer',
        'soft_skills_sesi1_peserta' => 'integer',
        'soft_skills_sesi2_peserta' => 'integer',
    ];

    public function graduan()
    {
        return $this->hasMany(Talent::class, 'id_syarikat_penempatan', 'id_syarikat');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_syarikat_penempatan', 'id_syarikat');
    }

    public function applicantRequests()
    {
        return $this->hasMany(ApplicantRequest::class, 'placement_company_id', 'id_syarikat');
    }

    /**
     * Training Compliance Matrix rating.
     */
    public function getTrainingRatingAttribute(): string
    {
        if ($this->soft_skills_sesi1_status !== 'Selesai') {
            return 'Perlu Tindakan';
        }

        if ($this->soft_skills_sesi2_status !== 'Selesai') {
            return 'Dalam Proses';
        }

        $pct = $this->training_compliance_pct;
        if ($pct > 90) return 'Cemerlang';
        if ($pct >= 80) return 'Baik';
        if ($pct >= 70) return 'Memuaskan';
        return 'Perlu Penambahbaikan';
    }

    public static function generateId(): string
    {
        $last = static::orderByDesc('id_syarikat')->first();

        if ($last && preg_match('/SPTAN_(\d+)/', $last->id_syarikat, $matches)) {
            $newNum = str_pad((int) $matches[1] + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNum = '001';
        }

        return "SPTAN_{$newNum}";
    }
}
