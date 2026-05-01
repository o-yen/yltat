<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicant_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('applicant_requests', 'implementing_company_id')) {
                $table->string('implementing_company_id')->nullable()->after('talent_id');
                $table->index(['implementing_company_id', 'status'], 'applicant_requests_pelaksana_status_index');
            }
        });

        if (Schema::hasColumn('applicant_requests', 'placement_company_id')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE applicant_requests MODIFY placement_company_id VARCHAR(255) NULL');
            }
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                UPDATE applicant_requests ar
                JOIN users u ON u.id = ar.requested_by_user_id
                SET ar.implementing_company_id = u.id_pelaksana
                WHERE ar.implementing_company_id IS NULL
                  AND u.id_pelaksana IS NOT NULL
            ");

            DB::statement("
                UPDATE applicant_requests ar
                JOIN talents t ON t.id = ar.talent_id
                SET ar.implementing_company_id = t.id_pelaksana
                WHERE ar.implementing_company_id IS NULL
                  AND t.id_pelaksana IS NOT NULL
            ");
        }
    }

    public function down(): void
    {
        Schema::table('applicant_requests', function (Blueprint $table) {
            if (Schema::hasColumn('applicant_requests', 'implementing_company_id')) {
                if (method_exists(Schema::class, 'hasIndex') && Schema::hasIndex('applicant_requests', 'applicant_requests_pelaksana_status_index')) {
                    $table->dropIndex('applicant_requests_pelaksana_status_index');
                }
                $table->dropColumn('implementing_company_id');
            }
        });
    }
};
