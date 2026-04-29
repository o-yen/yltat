<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetTransaction extends Model
{
    use HasFactory;

    protected $table = 'budget_transactions';

    protected $fillable = [
        'placement_id',
        'talent_id',
        'company_id',
        'transaction_date',
        'category',
        'description',
        'amount',
        'status',
        'reference_no',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function placement()
    {
        return $this->belongsTo(Placement::class);
    }

    public function talent()
    {
        return $this->belongsTo(Talent::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
