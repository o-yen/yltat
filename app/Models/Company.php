<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $fillable = [
        'company_code',
        'company_name',
        'registration_no',
        'industry',
        'address',
        'contact_person',
        'contact_email',
        'contact_phone',
        'agreement_status',
        'status',
        'notes',
    ];

    public function placements()
    {
        return $this->hasMany(Placement::class);
    }

    public function linkedUser()
    {
        return $this->hasOne(User::class);
    }

    public function budgetAllocations()
    {
        return $this->hasMany(BudgetAllocation::class);
    }

    public function budgetTransactions()
    {
        return $this->hasMany(BudgetTransaction::class);
    }

    public function activePlacements()
    {
        return $this->placements()->whereIn('placement_status', ['active', 'confirmed']);
    }

    public static function generateCode(): string
    {
        $lastCompany = static::orderBy('company_code', 'desc')->first();

        if ($lastCompany && preg_match('/SYR-(\d+)/', $lastCompany->company_code, $matches)) {
            $newNum = str_pad((int)$matches[1] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }

        return "SYR-{$newNum}";
    }
}
