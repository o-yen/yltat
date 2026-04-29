<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Placement extends Model
{
    use HasFactory;

    protected $table = 'placements';

    protected $fillable = [
        'talent_id',
        'company_id',
        'batch_id',
        'department',
        'supervisor_name',
        'supervisor_email',
        'start_date',
        'end_date',
        'duration_months',
        'monthly_stipend',
        'additional_cost',
        'placement_status',
        'programme_type',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_stipend' => 'decimal:2',
        'additional_cost' => 'decimal:2',
    ];

    public function talent()
    {
        return $this->belongsTo(Talent::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function batch()
    {
        return $this->belongsTo(IntakeBatch::class, 'batch_id');
    }

    public function feedback()
    {
        return $this->hasMany(InternshipFeedback::class);
    }

    public function budgetTransactions()
    {
        return $this->hasMany(BudgetTransaction::class);
    }

    public function getRemainingMonthsAttribute(): int
    {
        if ($this->end_date->isFuture()) {
            return (int) Carbon::now()->diffInMonths($this->end_date, false);
        }
        return 0;
    }

    public function getProjectedRemainingAttribute(): float
    {
        return $this->monthly_stipend * $this->remaining_months;
    }

    public function getActualSpentAttribute(): float
    {
        return $this->budgetTransactions()->where('status', 'approved')->sum('amount');
    }
}
