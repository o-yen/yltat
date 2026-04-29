<?php

namespace Database\Seeders;

use App\Models\DimDate;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DimDateSeeder extends Seeder
{
    public function run(): void
    {
        $start = Carbon::parse('2025-01-01');
        $end = Carbon::parse('2026-12-31');

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        $shortMonths = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
        ];

        $batch = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $monthNo = $current->month;
            $quarter = 'Q' . ceil($monthNo / 3);

            $batch[] = [
                'date' => $current->format('Y-m-d'),
                'year' => $current->year,
                'quarter' => $quarter,
                'month_no' => $monthNo,
                'month' => $months[$monthNo],
                'month_year' => $shortMonths[$monthNo] . ' ' . $current->year,
                'month_start' => $current->copy()->startOfMonth()->format('Y-m-d'),
                'is_month_start' => $current->day === 1,
                'is_weekend' => $current->isWeekend(),
            ];

            // Insert in batches of 100 to avoid memory issues
            if (count($batch) >= 100) {
                DimDate::upsert($batch, ['date'], array_keys($batch[0]));
                $batch = [];
            }

            $current->addDay();
        }

        // Insert remaining
        if (count($batch) > 0) {
            DimDate::upsert($batch, ['date'], array_keys($batch[0]));
        }
    }
}
