<?php

namespace Aldeebhasan\LaravelEventTracker\Commands;

use Illuminate\Console\Command;

class LaravelEventTrackerCommand extends Command
{
    public $signature = 'tracker';
    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
