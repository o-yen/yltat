<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intake_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_name', 200);
            $table->date('start_date');
            $table->date('end_date');
            $table->year('year');
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intake_batches');
    }
};
