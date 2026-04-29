<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class SqlDumpRestoreSeeder extends Seeder
{
    /**
     * Runtime/infrastructure tables that should not be restored by a
     * post-migration data seeder.
     *
     * @var list<string>
     */
    private array $ignoredTables = [
        'migrations',
        'failed_jobs',
        'jobs',
        'job_batches',
        'cache',
        'cache_locks',
        'sessions',
        'password_reset_tokens',
        'password_reset_otps',
        'mobile_access_tokens',
        'mobile_device_tokens',
        'mobile_notifications',
        'sqlite_sequence',
    ];

    public function run(): void
    {
        $dumpPath = (string) env(
            'SQL_DUMP_SEEDER_PATH',
            database_path('seeders/data/original_dump.sql')
        );

        if (! File::exists($dumpPath)) {
            throw new RuntimeException("SQL dump file not found at [{$dumpPath}].");
        }

        $sql = File::get($dumpPath);
        $statements = $this->extractDataStatements($sql);

        if ($statements === []) {
            throw new RuntimeException(
                "No INSERT/REPLACE/UPDATE statements were found in [{$dumpPath}]."
            );
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach ($statements as $statement) {
                DB::unprepared($statement);
            }
        } catch (\Throwable $e) {
            throw new RuntimeException(
                "Failed while restoring SQL dump data from [{$dumpPath}].",
                previous: $e,
            );
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->command?->info("SQL dump data restored from [{$dumpPath}].");
    }

    /**
     * Keep only data-mutating statements so this seeder can run safely after
     * `migrate:fresh`, even when the dump also contains schema DDL.
     *
     * @return list<string>
     */
    private function extractDataStatements(string $sql): array
    {
        $sql = str_replace(["\r\n", "\r"], "\n", $sql);

        // Strip SQL dump comments/header noise before splitting.
        $sql = preg_replace('/^\s*--.*$/m', '', $sql) ?? $sql;
        $sql = preg_replace('/\/\*![\s\S]*?\*\//', '', $sql) ?? $sql;
        $sql = preg_replace('/\/\*[\s\S]*?\*\//', '', $sql) ?? $sql;

        $chunks = preg_split('/;\s*(?:\n|$)/', $sql) ?: [];
        $statements = [];

        foreach ($chunks as $chunk) {
            $statement = trim($chunk);

            if ($statement === '') {
                continue;
            }

            $upper = strtoupper($statement);

            if (
                str_starts_with($upper, 'INSERT INTO') ||
                str_starts_with($upper, 'REPLACE INTO') ||
                str_starts_with($upper, 'UPDATE ')
            ) {
                $table = $this->extractTableName($statement);

                if ($table !== null && in_array($table, $this->ignoredTables, true)) {
                    continue;
                }

                $statements[] = $statement . ';';
            }
        }

        return $statements;
    }

    private function extractTableName(string $statement): ?string
    {
        if (preg_match('/^(?:INSERT\s+INTO|REPLACE\s+INTO|UPDATE)\s+`?([a-zA-Z0-9_]+)`?/i', $statement, $matches)) {
            return strtolower($matches[1]);
        }

        return null;
    }
}
