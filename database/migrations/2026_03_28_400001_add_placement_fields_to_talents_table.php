<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->string('department', 200)->nullable()->after('jawatan');
            $table->string('supervisor_name', 200)->nullable()->after('department');
            $table->string('supervisor_email', 200)->nullable()->after('supervisor_name');
            $table->integer('duration_months')->nullable()->after('supervisor_email');
            $table->decimal('monthly_stipend', 10, 2)->nullable()->after('duration_months');
            $table->decimal('additional_cost', 10, 2)->nullable()->after('monthly_stipend');
            $table->string('programme_type', 100)->nullable()->after('additional_cost');
        });
    }

    public function down(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->dropColumn([
                'department', 'supervisor_name', 'supervisor_email',
                'duration_months', 'monthly_stipend', 'additional_cost', 'programme_type',
            ]);
        });
    }
};
