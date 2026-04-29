<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syarikat_pelaksana', function (Blueprint $table) {
            $table->string('id_pelaksana', 20)->primary();
            $table->string('nama_syarikat', 255);
            $table->string('projek_kontrak', 255)->nullable();
            $table->integer('jumlah_kuota_obligasi')->default(0);
            $table->integer('kuota_diluluskan')->default(0);
            $table->integer('kuota_digunakan')->default(0);
            $table->decimal('peruntukan_diluluskan', 15, 2)->default(0);
            $table->decimal('peruntukan_diguna', 15, 2)->default(0);
            $table->decimal('baki_peruntukan', 15, 2)->default(0);
            $table->string('status_surat_kuning', 30)->default('Belum Mula');
            $table->string('status_surat_biru', 30)->default('Belum Mula');
            $table->string('pic_syarikat', 200);
            $table->string('email_pic', 200);
            $table->string('status_dana', 30)->default('Mencukupi');
            $table->string('tahap_pematuhan', 50)->default('Baik');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syarikat_pelaksana');
    }
};
