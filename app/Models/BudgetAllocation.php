<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetAllocation extends Model
{
    use HasFactory;

    protected $table = 'budget_allocations';

    protected $fillable = [
        'fiscal_year',
        'batch_id',
        'company_id',
        'allocated_amount',
        'remarks',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
    ];

    public function batch()
    {
        return $this->belongsTo(IntakeBatch::class, 'batch_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
