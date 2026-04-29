<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->string('id_graduan', 20)->nullable()->unique()->after('id');
            $table->string('negeri', 100)->nullable()->after('address');
            $table->string('kelayakan', 150)->nullable()->after('gender');
            $table->date('tarikh_mula')->nullable()->after('jawatan');
            $table->date('tarikh_tamat')->nullable()->after('tarikh_mula');
            $table->string('status_aktif', 30)->nullable()->after('tarikh_tamat');
        });
    }

    public function down(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->dropUnique(['id_graduan']);
            $table->dropColumn(['id_graduan', 'negeri', 'kelayakan', 'tarikh_mula', 'tarikh_tamat', 'status_aktif']);
        });
    }
};
