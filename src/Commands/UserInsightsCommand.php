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

        try {
            $results = $handler->getUserInsights($from, $to, $event, $userId);
        } catch (TrackingException $e) {
            $this->output->error($e->getMessage());

            return self::FAILURE;
        }
        $this->output->title($results['title']);
        $this->output->text("Generated at:  {$results['generated_at']} ");
        $this->output->text("Between : {$results['period']['start']} & {$results['period']['end']}");
        $this->output->section("Top 3 users");
        $this->table(
            ["User", 'Event count'],
            array_map(fn($value, $key) => [$key, $value], $results['data']['top_3_users'], array_keys($results['data']['top_3_users'])),
        );

        $this->output->section("User Insights");
        $this->table(
            ["Day", 'Users'],
            array_map(
                fn($key, $value) => [$key, collect($value)->map(fn($val, $key) => "$key: $val")->implode(', ')],
                array_keys($results['data']['by_days']),
                $results['data']['by_days']
            ),
        );

        return self::SUCCESS;
    }
}
