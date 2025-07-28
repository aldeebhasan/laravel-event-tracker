<?php

namespace Aldeebhasan\LaravelEventTracker\Commands;

use Aldeebhasan\LaravelEventTracker\Factories\EventTrackerDriverFactory;
use Illuminate\Console\Command;

class StatisticCommand extends Command
{
    public $signature = 'events:statistics  {--from=} {--to=} {--event=}';
    public $description = 'My command';

    public function handle(): int
    {
        $event = $this->option('event');
        $from = $this->argument('from');
        $to = $this->argument('to');

        $handler = (new EventTrackerDriverFactory)->getInstance(config('event-tracker.driver'));

        dump($handler->statistic($event, $from, $to));

        return self::SUCCESS;
    }
}
