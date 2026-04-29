<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('placement_id')->constrained('placements')->onDelete('cascade');
            $table->enum('feedback_from', ['company', 'talent', 'yltat']);
            $table->tinyInteger('score_technical')->nullable();
            $table->tinyInteger('score_communication')->nullable();
            $table->tinyInteger('score_discipline')->nullable();
            $table->tinyInteger('score_problem_solving')->nullable();
            $table->tinyInteger('score_professionalism')->nullable();
            $table->text('comments')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_feedback');
    }
};
