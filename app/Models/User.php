<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'role_id',
        'talent_id',
        'company_id',
        'id_pelaksana',
        'id_syarikat_penempatan',
        'status',
        'language',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function talent()
    {
        return $this->belongsTo(Talent::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function syarikatPelaksana()
    {
        return $this->belongsTo(SyarikatPelaksana::class, 'id_pelaksana', 'id_pelaksana');
    }

    public function syarikatPenempatan()
    {
        return $this->belongsTo(SyarikatPenempatan::class, 'id_syarikat_penempatan', 'id_syarikat');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function mobileAccessTokens()
    {
        return $this->hasMany(MobileAccessToken::class);
    }

    public function mobileNotifications()
    {
        return $this->hasMany(MobileNotification::class);
    }

    public function mobileDeviceTokens()
    {
        return $this->hasMany(MobileDeviceToken::class);
    }

    public function applicantRequests()
    {
        return $this->hasMany(ApplicantRequest::class, 'requested_by_user_id');
    }

    public function reviewedApplicantRequests()
    {
        return $this->hasMany(ApplicantRequest::class, 'reviewed_by_user_id');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->role_name === $roleName;
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array($this->role->role_name, $roles);
    }
}
