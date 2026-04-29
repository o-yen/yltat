<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_records', function (Blueprint $table) {
            $table->id();
            $table->string('id_training', 20)->unique();
            $table->string('id_syarikat', 20)->index();
            $table->string('nama_syarikat', 255);
            $table->string('jenis_training', 30);
            $table->string('tajuk_training', 255);
            $table->string('sesi', 20);
            $table->date('tarikh_training');
            $table->integer('durasi_jam')->default(8);
            $table->string('lokasi', 255)->nullable();
            $table->string('trainer_name', 200);
            $table->string('trainer_type', 20)->default('Internal');
            $table->integer('jumlah_dijemput')->default(0);
            $table->integer('jumlah_hadir')->default(0);
            $table->decimal('kadar_kehadiran_pct', 5, 2)->default(0);
            $table->text('topik_covered')->nullable();
            $table->decimal('pre_assessment_avg', 4, 2)->default(0);
            $table->decimal('post_assessment_avg', 4, 2)->default(0);
            $table->decimal('improvement_pct', 5, 2)->default(0);
            $table->decimal('skor_kepuasan', 4, 2)->default(0);
            $table->decimal('budget_allocated', 10, 2)->default(0);
            $table->decimal('budget_spent', 10, 2)->default(0);
            $table->string('status', 20)->default('Dirancang');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_syarikat')->references('id_syarikat')->on('syarikat_penempatan')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_records');
    }
};
