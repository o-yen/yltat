<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_surat', function (Blueprint $table) {
            $table->id();
            $table->string('id_pelaksana', 20)->index();
            $table->string('jenis_surat', 20);
            $table->string('id_graduan', 20)->nullable();
            $table->string('nama_graduan', 255)->nullable();
            $table->string('status_surat', 30)->default('Belum Mula');
            $table->date('tarikh_mula_proses')->nullable();
            $table->date('tarikh_draft')->nullable();
            $table->date('tarikh_semakan')->nullable();
            $table->date('tarikh_tandatangan')->nullable();
            $table->date('tarikh_hantar')->nullable();
            $table->date('tarikh_siap')->nullable();
            $table->string('pic_responsible', 200);
            $table->text('isu_halangan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_pelaksana')->references('id_pelaksana')->on('syarikat_pelaksana')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_surat');
    }
};
