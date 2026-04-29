<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->string('kategori', 50)->nullable()->after('status')
                  ->comment('Anak ATM / Anak Veteran / Anak Awam MINDEF');
            $table->string('status_penyerapan_6bulan', 30)->nullable()->after('kategori')
                  ->comment('Diserap / Tidak Diserap / Belum Layak');
            $table->string('id_pelaksana', 20)->nullable()->after('status_penyerapan_6bulan');
            $table->string('id_syarikat_penempatan', 20)->nullable()->after('id_pelaksana');
            $table->string('jawatan', 200)->nullable()->after('id_syarikat_penempatan')
                  ->comment('Job position at placement company');

            $table->foreign('id_pelaksana')->references('id_pelaksana')->on('syarikat_pelaksana')->nullOnDelete();
            $table->foreign('id_syarikat_penempatan')->references('id_syarikat')->on('syarikat_penempatan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->dropForeign(['id_pelaksana']);
            $table->dropForeign(['id_syarikat_penempatan']);
            $table->dropColumn(['kategori', 'status_penyerapan_6bulan', 'id_pelaksana', 'id_syarikat_penempatan', 'jawatan']);
        });
    }
};
