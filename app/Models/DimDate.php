<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DimDate extends Model
{
    protected $table = 'dim_dates';
    protected $primaryKey = 'date';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'date',
        'year',
        'quarter',
        'month_no',
        'month',
        'month_year',
        'month_start',
        'is_month_start',
        'is_weekend',
    ];

    protected $casts = [
        'date' => 'date',
        'month_start' => 'date',
        'is_month_start' => 'boolean',
        'is_weekend' => 'boolean',
    ];

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->where('year', $year)->where('month_no', $month);
    }

    public function scopeWorkingDays($query)
    {
        return $query->where('is_weekend', false);
    }
}
