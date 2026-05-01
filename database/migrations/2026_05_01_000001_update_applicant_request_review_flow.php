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
            try {
                $table->dropUnique('applicant_requests_talent_id_implementing_company_id_unique');
            } catch (Throwable $e) {
                // Older deployments may not have this unique index.
            }

            try {
                $table->index(['talent_id', 'implementing_company_id'], 'applicant_requests_talent_pelaksana_index');
            } catch (Throwable $e) {
                // Index may already exist on fresh installs.
            }
        });

        DB::table('applicant_requests')
            ->where('status', 'pending')
            ->update(['status' => 'pending_implementation_review']);

        DB::table('applicant_requests')
            ->where('status', 'rejected')
            ->update(['status' => 'rejected_by_admin']);
    }

    public function down(): void
    {
        DB::table('applicant_requests')
            ->where('status', 'pending_implementation_review')
            ->update(['status' => 'pending']);

        DB::table('applicant_requests')
            ->whereIn('status', ['rejected_by_implementation', 'rejected_by_admin'])
            ->update(['status' => 'rejected']);

        Schema::table('applicant_requests', function (Blueprint $table) {
            try {
                $table->dropIndex('applicant_requests_talent_pelaksana_index');
            } catch (Throwable $e) {
                // Index may not exist.
            }
        });
    }
};
