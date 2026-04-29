<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogbookUpload extends Model
{
    use HasFactory;

    protected $table = 'logbook_uploads';

    protected $fillable = [
        'id_graduan', 'nama_graduan', 'id_syarikat', 'nama_syarikat',
        'bulan', 'tahun', 'status_logbook', 'tarikh_upload',
        'link_file_logbook', 'file_name', 'status_semakan', 'komen_mentor',
        'tarikh_semakan', 'nama_mentor',
    ];

    protected $casts = [
        'tarikh_upload' => 'date',
        'tarikh_semakan' => 'date',
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

    public function getIsLateAttribute(): bool
    {
        return $this->status_logbook === 'Lewat';
    }
}
