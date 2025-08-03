<?php

namespace Aldeebhasan\LaravelEventTracker\Commands;

use Aldeebhasan\LaravelEventTracker\Exceptions\TrackingException;
use Aldeebhasan\LaravelEventTracker\Factories\EventTrackerDriverFactory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class StatisticCommand extends Command
{
    public $signature = 'event-tracker:statistics  {--from=} {--to=} {--event=} {--user_id=}';
    public $description = 'My command';

    /**
     * @throws TrackingException
     */
    public function handle(): int
    {
        $userId = $this->option('user_id');
        $event = $this->option('event');
        $from = Carbon::parse($this->option('from') ?? now()->subDay())->startOfDay()->toDateString();
        $to = Carbon::parse($this->option('to') ?? now())->endOfDay()->toDateString();

        $handler = (new EventTrackerDriverFactory)->getInstance(config('event-tracker.driver'));

        try {
            $results = $handler->getStatistic($from, $to, $event, $userId);
        } catch (TrackingException $e) {
            $this->output->error($e->getMessage());

            return self::FAILURE;
        }

        $this->output->title($results['title']);
        $this->output->text("Generated at:  {$results['generated_at']} ");
        $this->output->text("Between : {$results['period']['start']} & {$results['period']['end']}");
        $this->table(
            ["Key", 'Count'],
            array_map(fn($value, $key) => [str($key)->title(), $value], $results['data'], array_keys($results['data'])),
        );

        return self::SUCCESS;
    }
}
