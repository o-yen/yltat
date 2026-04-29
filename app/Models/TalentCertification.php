<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TalentCertification extends Model
{
    use HasFactory;

    protected $table = 'talent_certifications';

    protected $fillable = [
        'talent_id',
        'certification_name',
        'issuer',
        'issue_date',
        'expiry_date',
        'file_path',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function talent()
    {
        return $this->belongsTo(Talent::class);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
