<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('isu_risiko', function (Blueprint $table) {
            $table->id();
            $table->string('id_isu', 20)->unique();
            $table->date('tarikh_isu');
            $table->string('id_pelaksana', 20)->nullable()->index();
            $table->string('id_syarikat', 20)->nullable()->index();
            $table->string('kategori_isu', 50);
            $table->text('butiran_isu');
            $table->string('tahap_risiko', 20);
            $table->string('status', 30)->default('Baru');
            $table->string('pic', 200);
            $table->text('tindakan_diambil')->nullable();
            $table->date('tarikh_tindakan')->nullable();
            $table->date('tarikh_tutup')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_pelaksana')->references('id_pelaksana')->on('syarikat_pelaksana')->nullOnDelete();
            $table->foreign('id_syarikat')->references('id_syarikat')->on('syarikat_penempatan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('isu_risiko');
    }
};
