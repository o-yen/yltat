<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syarikat_penempatan', function (Blueprint $table) {
            $table->string('id_syarikat', 20)->primary();
            $table->string('nama_syarikat', 255);
            $table->string('jenis_syarikat', 50)->default('Rakan Kolaborasi');
            $table->string('sektor_industri', 100);
            $table->integer('kuota_dipersetujui')->default(0);
            $table->integer('jumlah_graduan_ditempatkan')->default(0);
            $table->string('pic', 200);
            $table->string('no_telefon_pic', 30);
            $table->string('email_pic', 200);
            $table->string('laporan_bulanan', 30)->default('Lengkap');
            $table->string('status_pematuhan', 50)->default('Baik');
            $table->text('catatan')->nullable();
            $table->string('soft_skills_sesi1_status', 30)->default('Belum Mula');
            $table->date('soft_skills_sesi1_tarikh')->nullable();
            $table->integer('soft_skills_sesi1_peserta')->default(0);
            $table->string('soft_skills_sesi2_status', 30)->default('Belum Mula');
            $table->date('soft_skills_sesi2_tarikh')->nullable();
            $table->integer('soft_skills_sesi2_peserta')->default(0);
            $table->decimal('training_compliance_pct', 5, 2)->default(0);
            $table->string('status_training', 30)->default('Belum Mula');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syarikat_penempatan');
    }
};
