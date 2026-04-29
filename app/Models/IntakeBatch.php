<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntakeBatch extends Model
{
    use HasFactory;

    protected $table = 'intake_batches';

    protected $fillable = [
        'batch_name',
        'start_date',
        'end_date',
        'year',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function placements()
    {
        return $this->hasMany(Placement::class, 'batch_id');
    }

    public function budgetAllocations()
    {
        return $this->hasMany(BudgetAllocation::class, 'batch_id');
    }
}
