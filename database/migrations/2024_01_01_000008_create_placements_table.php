<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talent_id')->constrained('talents')->onDelete('restrict');
            $table->foreignId('company_id')->constrained('companies')->onDelete('restrict');
            $table->foreignId('batch_id')->nullable()->constrained('intake_batches')->onDelete('set null');
            $table->string('department', 200)->nullable();
            $table->string('supervisor_name', 200)->nullable();
            $table->string('supervisor_email', 200)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->tinyInteger('duration_months')->nullable();
            $table->decimal('monthly_stipend', 10, 2)->default(0);
            $table->decimal('additional_cost', 10, 2)->default(0);
            $table->string('placement_status', 30)->default('planned');
            $table->string('programme_type', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placements');
    }
};
