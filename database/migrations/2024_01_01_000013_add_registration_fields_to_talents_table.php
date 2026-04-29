<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->string('background_type')->nullable()->after('notes'); // anak_atm, anak_veteran_atm, anak_awam_mindef
            $table->string('guardian_name')->nullable()->after('background_type');
            $table->string('guardian_ic')->nullable()->after('guardian_name');
            $table->string('guardian_military_no')->nullable()->after('guardian_ic');
            $table->string('guardian_relationship')->nullable()->after('guardian_military_no');
            $table->string('highest_qualification')->nullable()->after('guardian_relationship'); // diploma, ijazah, sarjana, phd, lain
            $table->json('preferred_sectors')->nullable()->after('highest_qualification');
            $table->json('preferred_locations')->nullable()->after('preferred_sectors');
            $table->boolean('currently_employed')->default(false)->after('preferred_locations');
            $table->date('available_start_date')->nullable()->after('currently_employed');
            $table->boolean('pdpa_consent')->default(false)->after('available_start_date');
            $table->string('declaration_signature')->nullable()->after('pdpa_consent');
            $table->text('rejection_reason')->nullable()->after('declaration_signature');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('rejection_reason');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->dropColumn([
                'background_type',
                'guardian_name',
                'guardian_ic',
                'guardian_military_no',
                'guardian_relationship',
                'highest_qualification',
                'preferred_sectors',
                'preferred_locations',
                'currently_employed',
                'available_start_date',
                'pdpa_consent',
                'declaration_signature',
                'rejection_reason',
                'reviewed_by',
                'reviewed_at',
            ]);
        });
    }
};
