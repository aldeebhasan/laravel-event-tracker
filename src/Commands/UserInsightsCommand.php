<?php

namespace Aldeebhasan\LaravelEventTracker\Commands;

use Aldeebhasan\LaravelEventTracker\Factories\EventTrackerDriverFactory;

class UserInsightsCommand extends AbstractCommand
{
    public $signature = 'event-tracker:user-insights
                        {--from= : Start date (YYYY-MM-DD)}
                        {--to= : End date (YYYY-MM-DD)}
                        {--event= : Specific event name}
                        {--user_id= : Specific user id}';
    public $description = 'Show the user insights within a specific period of time';

    protected function getResult(): array
    {
        $handler = (new EventTrackerDriverFactory)->getInstance(config('event-tracker.driver'));

        return $handler->getUserInsights($this->from, $this->to, $this->event, $this->userId);
    }

    protected function handleOutput(array $results): void
    {
        $this->output->title($results['title']);
        $this->output->text("Generated at:  {$results['generated_at']} ");
        $this->output->text("Between : {$results['period']['start']} & {$results['period']['end']}");
        $this->output->section("Top 3 users");
        $this->table(
            ["User", 'Event count'],
            array_map(fn($value, $key) => [$key, $value], $results['data']['top_3_users'], array_keys($results['data']['top_3_users'])),
        );

        $rows = collect($results['data']['by_days'])->map(function ($items, $key) {
            $results = [];
            $day = $key;
            foreach ($items as $user => $count) {
                $results[] = [$day, $user, $count];
                $day = '';
            }

            return $results;
        })->collapse();

        $this->output->section("User Insights");
        $this->table(["Day", 'User', 'Count'], $rows);
    }
}
