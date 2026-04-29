<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbook_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('id_graduan', 20)->index();
            $table->string('nama_graduan', 255);
            $table->string('id_syarikat', 20)->nullable()->index();
            $table->string('nama_syarikat', 255)->nullable();
            $table->string('bulan', 30);
            $table->integer('tahun');
            $table->string('status_logbook', 30)->default('Belum Dikemukakan');
            $table->date('tarikh_upload')->nullable();
            $table->string('link_file_logbook', 500)->nullable();
            $table->string('status_semakan', 30)->default('Belum Disemak');
            $table->text('komen_mentor')->nullable();
            $table->date('tarikh_semakan')->nullable();
            $table->string('nama_mentor', 200)->nullable();
            $table->timestamps();

            $table->unique(['id_graduan', 'bulan', 'tahun']);
            $table->foreign('id_syarikat')->references('id_syarikat')->on('syarikat_penempatan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_uploads');
    }
};
