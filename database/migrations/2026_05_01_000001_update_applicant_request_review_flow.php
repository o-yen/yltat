<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->indexExists('applicant_requests_talent_id_implementing_company_id_unique')) {
            DB::statement('ALTER TABLE `applicant_requests` DROP INDEX `applicant_requests_talent_id_implementing_company_id_unique`');
        }

        if (!$this->indexExists('applicant_requests_talent_pelaksana_index')) {
            DB::statement('CREATE INDEX `applicant_requests_talent_pelaksana_index` ON `applicant_requests` (`talent_id`, `implementing_company_id`)');
        }

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

        if ($this->indexExists('applicant_requests_talent_pelaksana_index')) {
            DB::statement('ALTER TABLE `applicant_requests` DROP INDEX `applicant_requests_talent_pelaksana_index`');
        }
    }

    private function indexExists(string $indexName): bool
    {
        return !empty(DB::select(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [DB::getDatabaseName(), 'applicant_requests', $indexName]
        ));
    }
};
