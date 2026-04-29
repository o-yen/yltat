<?php

namespace App\Console\Commands;

use App\Services\AutoFlaggingService;
use Illuminate\Console\Command;

class RunAutoFlagging extends Command
{
    protected $signature = 'protege:auto-flag';
    protected $description = 'Run all auto-flagging business rules (payment overdue, low attendance, budget overrun, etc.)';

    public function handle(): int
    {
        $this->info('Running auto-flagging rules...');

        $service = new AutoFlaggingService();
        $count = $service->runAll();

        $this->info("Done. {$count} new issues created.");

        return self::SUCCESS;
    }
}
