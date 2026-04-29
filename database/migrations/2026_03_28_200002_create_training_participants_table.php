<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_participants', function (Blueprint $table) {
            $table->id();
            $table->string('id_record', 20)->unique();
            $table->string('id_training', 20)->index();
            $table->string('id_graduan', 20);
            $table->string('nama_graduan', 255);
            $table->string('status_kehadiran', 20)->default('Tidak Hadir');
            $table->decimal('pre_assessment_score', 4, 2)->default(0);
            $table->decimal('post_assessment_score', 4, 2)->default(0);
            $table->decimal('improvement_pct', 5, 2)->default(0);
            $table->boolean('certificate_issued')->default(false);
            $table->boolean('feedback_submitted')->default(false);
            $table->boolean('action_plan_submitted')->default(false);
            $table->text('mentor_feedback')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_training')->references('id_training')->on('training_records')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_participants');
    }
};
