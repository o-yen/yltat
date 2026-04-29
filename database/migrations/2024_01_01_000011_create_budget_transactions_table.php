<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('placement_id')->nullable()->constrained('placements')->onDelete('set null');
            $table->foreignId('talent_id')->nullable()->constrained('talents')->onDelete('set null');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->date('transaction_date');
            $table->string('category', 100);
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('status', 30)->default('approved');
            $table->string('reference_no', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_transactions');
    }
};
