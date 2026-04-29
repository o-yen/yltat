<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Talent extends Model
{
    use HasFactory;

    protected $table = 'talents';

    protected $fillable = [
        'id_graduan',
        'talent_code',
        'profile_photo',
        'full_name',
        'ic_passport_no',
        'date_of_birth',
        'gender',
        'kelayakan',
        'email',
        'phone',
        'address',
        'negeri',
        'university',
        'programme',
        'cgpa',
        'graduation_year',
        'skills_text',
        'profile_summary',
        'public_visibility',
        'status',
        'notes',
        // Registration fields
        'background_type',
        'guardian_name',
        'guardian_ic',
        'guardian_military_no',
        'guardian_relationship',
        'highest_qualification',
        'preferred_sectors',
        'preferred_locations',
        'currently_employed',
        'available_start_date',
        'pdpa_consent',
        'declaration_signature',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        // PROTEGE RTW fields
        'kategori',
        'status_penyerapan_6bulan',
        'id_pelaksana',
        'id_syarikat_penempatan',
        'jawatan',
        'tarikh_mula',
        'tarikh_tamat',
        'status_aktif',
        // Placement fields (merged from old Placement module)
        'department',
        'supervisor_name',
        'supervisor_email',
        'duration_months',
        'monthly_stipend',
        'additional_cost',
        'programme_type',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'tarikh_mula' => 'date',
        'tarikh_tamat' => 'date',
        'public_visibility' => 'boolean',
        'cgpa' => 'decimal:2',
        'preferred_sectors' => 'array',
        'preferred_locations' => 'array',
        'currently_employed' => 'boolean',
        'pdpa_consent' => 'boolean',
        'available_start_date' => 'date',
        'reviewed_at' => 'datetime',
        'monthly_stipend' => 'decimal:2',
        'additional_cost' => 'decimal:2',
        'duration_months' => 'integer',
    ];

    public function documents()
    {
        return $this->hasMany(TalentDocument::class);
    }

    public function certifications()
    {
        return $this->hasMany(TalentCertification::class);
    }

    public function placements()
    {
        return $this->hasMany(Placement::class);
    }

    public function linkedUser()
    {
        return $this->hasOne(User::class);
    }

    public function budgetTransactions()
    {
        return $this->hasMany(BudgetTransaction::class);
    }

    public function dailyLogs()
    {
        return $this->hasMany(DailyLog::class);
    }

    public function syarikatPelaksana()
    {
        return $this->belongsTo(SyarikatPelaksana::class, 'id_pelaksana', 'id_pelaksana');
    }

    public function syarikatPenempatan()
    {
        return $this->belongsTo(SyarikatPenempatan::class, 'id_syarikat_penempatan', 'id_syarikat');
    }

    public function applicantRequests()
    {
        return $this->hasMany(ApplicantRequest::class);
    }

    public function getActiveplacementAttribute()
    {
        return $this->placements()->whereIn('placement_status', ['active', 'confirmed'])->first();
    }

    public function getResolvedStatusAttribute(): ?string
    {
        return $this->status_aktif ?: $this->status;
    }

    public static function generateCode(): string
    {
        $year = date('Y');
        $lastTalent = static::where('talent_code', 'like', "YLTAT-{$year}-%")
            ->orderBy('talent_code', 'desc')
            ->first();

        if ($lastTalent) {
            $lastNum = (int) substr($lastTalent->talent_code, -4);
            $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }

        return "YLTAT-{$year}-{$newNum}";
    }
}
