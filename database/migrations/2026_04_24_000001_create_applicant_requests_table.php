<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talent_id')->constrained('talents')->cascadeOnDelete();
            $table->string('implementing_company_id')->nullable();
            $table->foreign('implementing_company_id')
                ->references('id_pelaksana')
                ->on('syarikat_pelaksana')
                ->nullOnDelete();
            $table->string('placement_company_id')->nullable();
            $table->foreign('placement_company_id')
                ->references('id_syarikat')
                ->on('syarikat_penempatan')
                ->nullOnDelete();
            $table->foreignId('requested_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending_implementation_review');
            $table->text('request_message')->nullable();
            $table->text('review_notes')->nullable();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['talent_id', 'placement_company_id']);
            $table->index(['talent_id', 'implementing_company_id']);
            $table->index(['implementing_company_id', 'status']);
            $table->index(['placement_company_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_requests');
    }
};
