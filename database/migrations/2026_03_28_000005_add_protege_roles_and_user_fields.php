<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add pelaksana and penempatan links to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_pelaksana', 20)->nullable()->after('company_id');
            $table->string('id_syarikat_penempatan', 20)->nullable()->after('id_pelaksana');

            $table->foreign('id_pelaksana')->references('id_pelaksana')->on('syarikat_pelaksana')->nullOnDelete();
            $table->foreign('id_syarikat_penempatan')->references('id_syarikat')->on('syarikat_penempatan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_pelaksana']);
            $table->dropForeign(['id_syarikat_penempatan']);
            $table->dropColumn(['id_pelaksana', 'id_syarikat_penempatan']);
        });
    }
};
