<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_dashboard', function (Blueprint $table) {
            $table->id();
            $table->string('bulan', 30);
            $table->integer('tahun');
            $table->integer('total_graduan_aktif')->default(0);
            $table->integer('total_graduan_tamat_6bulan')->default(0);
            $table->integer('graduan_diserap_6bulan')->default(0);
            $table->decimal('retention_rate_pct', 5, 2)->default(0);
            $table->integer('total_bayaran_selesai')->default(0);
            $table->integer('total_bayaran_lewat')->default(0);
            $table->decimal('avg_kehadiran_pct', 5, 2)->default(0);
            $table->decimal('avg_prestasi_score', 4, 2)->default(0);
            $table->decimal('surat_kuning_siap_pct', 5, 2)->default(0);
            $table->decimal('surat_biru_siap_pct', 5, 2)->default(0);
            $table->decimal('logbook_submitted_pct', 5, 2)->default(0);
            $table->integer('isu_kritikal_active')->default(0);
            $table->decimal('budget_utilization_pct', 5, 2)->default(0);
            $table->integer('training_sessions_completed')->default(0);
            $table->decimal('training_compliance_rate_pct', 5, 2)->default(0);
            $table->decimal('avg_training_satisfaction', 4, 2)->default(0);
            $table->decimal('avg_skill_improvement_pct', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_dashboard');
    }
};
