<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kewangan_elaun', function (Blueprint $table) {
            $table->id();
            $table->string('id_graduan', 20)->index();
            $table->string('id_pelaksana', 20)->nullable()->index();
            $table->string('bulan', 30);
            $table->integer('tahun');
            $table->date('tarikh_mula_kerja')->nullable();
            $table->date('tarikh_akhir_kerja')->nullable();
            $table->integer('hari_bekerja_sebenar')->default(0);
            $table->integer('hari_dalam_bulan')->default(30);
            $table->decimal('elaun_penuh', 10, 2)->default(1600);
            $table->decimal('elaun_prorate', 10, 2)->default(0);
            $table->string('status_bayaran', 30)->default('Dalam Proses');
            $table->date('tarikh_bayar')->nullable();
            $table->date('tarikh_jangka_bayar')->nullable();
            $table->integer('hari_lewat')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['id_graduan', 'bulan', 'tahun']);
            $table->foreign('id_pelaksana')->references('id_pelaksana')->on('syarikat_pelaksana')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kewangan_elaun');
    }
};
