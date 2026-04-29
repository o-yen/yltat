<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('status_surat', function (Blueprint $table) {
            $table->string('file_attachment', 500)->nullable()->after('catatan');
            $table->string('file_name', 255)->nullable()->after('file_attachment');
        });
    }

    public function down(): void
    {
        Schema::table('status_surat', function (Blueprint $table) {
            $table->dropColumn(['file_attachment', 'file_name']);
        });
    }
};
