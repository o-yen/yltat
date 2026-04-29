<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadiran_prestasi', function (Blueprint $table) {
            $table->id();
            $table->string('id_graduan', 20)->index();
            $table->string('id_syarikat', 20)->nullable()->index();
            $table->string('id_pelaksana', 20)->nullable()->index();
            $table->string('bulan', 30);
            $table->integer('tahun');
            $table->decimal('kehadiran_pct', 5, 2)->default(0);
            $table->integer('hari_hadir')->default(0);
            $table->integer('hari_bekerja')->default(0);
            $table->integer('skor_prestasi')->default(0)->comment('1-10');
            $table->text('komen_mentor')->nullable();
            $table->string('status_logbook', 30)->default('Belum Dikemukakan');
            $table->timestamps();

            $table->unique(['id_graduan', 'bulan', 'tahun']);
            $table->foreign('id_syarikat')->references('id_syarikat')->on('syarikat_penempatan')->nullOnDelete();
            $table->foreign('id_pelaksana')->references('id_pelaksana')->on('syarikat_pelaksana')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadiran_prestasi');
    }
};
