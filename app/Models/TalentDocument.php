<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TalentDocument extends Model
{
    use HasFactory;

    protected $table = 'talent_documents';

    public $timestamps = false;

    protected $fillable = [
        'talent_id',
        'document_type',
        'file_name',
        'file_path',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function talent()
    {
        return $this->belongsTo(Talent::class);
    }
}
