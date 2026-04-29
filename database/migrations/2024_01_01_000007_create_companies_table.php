<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 50)->unique();
            $table->string('company_name', 255);
            $table->string('registration_no', 100)->nullable();
            $table->string('industry', 150)->nullable();
            $table->text('address')->nullable();
            $table->string('contact_person', 200);
            $table->string('contact_email', 200);
            $table->string('contact_phone', 30);
            $table->string('agreement_status', 30)->default('pending');
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
