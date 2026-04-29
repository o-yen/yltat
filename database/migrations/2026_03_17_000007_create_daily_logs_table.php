<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talent_id')->constrained('talents')->cascadeOnDelete();
            $table->foreignId('placement_id')->nullable()->constrained('placements')->nullOnDelete();
            $table->date('log_date');
            $table->text('activities');
            $table->text('challenges')->nullable();
            $table->text('learnings')->nullable();
            $table->enum('mood', ['great', 'good', 'neutral', 'tired', 'difficult'])->default('good');
            $table->enum('status', ['draft', 'submitted'])->default('submitted');
            $table->timestamps();

            $table->unique(['talent_id', 'log_date'], 'unique_talent_daily_log');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};
