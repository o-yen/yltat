<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'talent_id',
        'implementing_company_id',
        'placement_company_id',
        'requested_by_user_id',
        'status',
        'request_message',
        'review_notes',
        'reviewed_by_user_id',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function talent()
    {
        return $this->belongsTo(Talent::class);
    }

    public function placementCompany()
    {
        return $this->belongsTo(SyarikatPenempatan::class, 'placement_company_id', 'id_syarikat');
    }

    public function implementingCompany()
    {
        return $this->belongsTo(SyarikatPelaksana::class, 'implementing_company_id', 'id_pelaksana');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
