<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING_IMPLEMENTATION_REVIEW = 'pending_implementation_review';
    public const STATUS_REJECTED_BY_IMPLEMENTATION = 'rejected_by_implementation';
    public const STATUS_PENDING_ADMIN_APPROVAL = 'pending_admin_approval';
    public const STATUS_REJECTED_BY_ADMIN = 'rejected_by_admin';
    public const STATUS_APPROVED = 'approved';

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

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING_IMPLEMENTATION_REVIEW => 'Pending Implementation Review',
            self::STATUS_REJECTED_BY_IMPLEMENTATION => 'Rejected by Implementation Company',
            self::STATUS_PENDING_ADMIN_APPROVAL => 'Pending Admin Approval',
            self::STATUS_REJECTED_BY_ADMIN => 'Rejected by Admin / PMO',
            self::STATUS_APPROVED => 'Approved',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

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
