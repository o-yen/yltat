<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dim_dates', function (Blueprint $table) {
            $table->date('date')->primary();
            $table->integer('year')->index();
            $table->string('quarter', 5);
            $table->integer('month_no');
            $table->string('month', 20);
            $table->string('month_year', 20);
            $table->date('month_start');
            $table->boolean('is_month_start')->default(false);
            $table->boolean('is_weekend')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dim_dates');
    }
};
