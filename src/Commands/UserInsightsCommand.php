<?php

namespace Aldeebhasan\LaravelEventTracker\Commands;

use Aldeebhasan\LaravelEventTracker\Exceptions\TrackingException;
use Aldeebhasan\LaravelEventTracker\Factories\EventTrackerDriverFactory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UserInsightsCommand extends Command
{
    public $signature = 'event-tracker:user-insights  {--from=} {--to=} {--event=} {--user_id=}';
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

        $results = $handler->getUserInsights($from, $to, $event, $userId);

        $this->output->title($results['title']);
        $this->output->info("Generated at:  {$results['generated_at']} ");
        $this->output->info("Between : {$results['period']['start']} & {$results['period']['end']}");
        $this->output->info("-------------------------------");
        $this->output->info("Top 3 events");
        $this->table(
            ["Event", 'Count'],
            array_map(fn($value, $key) => [$key, $value], $results['data']['top_3_users'], array_keys($results['data']['top_3_users'])),
        );

        $this->output->info("User Insights");
        $this->table(
            ["Day", 'Events'],
            array_map(fn($value, $key) => [$key, implode('\n', $value)], $results['data']['by_days'], array_keys($results['data']['by_days'])),
        );

        return self::SUCCESS;
    }
}
