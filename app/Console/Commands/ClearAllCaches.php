<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAllCaches extends Command
{
    protected $signature = 'protege:clear-cache';
    protected $description = 'Clear all caches (config, route, view, app cache, compiled views)';

    public function handle(): int
    {
        $this->call('optimize:clear');

        // Delete compiled views manually (belt and suspenders)
        $viewDir = storage_path('framework/views');
        $files = glob("$viewDir/*.php");
        $count = count($files);
        foreach ($files as $f) {
            @unlink($f);
        }
        $this->info("Deleted $count compiled view files.");

        return self::SUCCESS;
    }
}
