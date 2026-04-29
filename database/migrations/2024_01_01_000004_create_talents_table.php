<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talents', function (Blueprint $table) {
            $table->id();
            $table->string('talent_code', 50)->unique();
            $table->string('full_name', 200);
            $table->string('ic_passport_no', 50);
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('university', 200)->nullable();
            $table->string('programme', 200)->nullable();
            $table->decimal('cgpa', 4, 2)->nullable();
            $table->string('graduation_year', 10)->nullable();
            $table->text('skills_text')->nullable();
            $table->text('profile_summary')->nullable();
            $table->boolean('public_visibility')->default(true);
            $table->string('status', 30)->default('applied');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talents');
    }
};
