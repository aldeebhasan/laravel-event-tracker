<?php

namespace Aldeebhasan\LaravelEventTracker\Commands;

use Aldeebhasan\LaravelEventTracker\Factories\EventTrackerDriverFactory;

class EventInsightsCommand extends AbstractCommand
{
    public $signature = 'event-tracker:event-insights
                        {--from= : Start date (YYYY-MM-DD)}
                        {--to= : End date (YYYY-MM-DD)}
                        {--event= : Specific event name}
                        {--user_id= : Specific user id}';
    public $description = 'Show the event insights for specific/all events within a specific period of time';

    protected function getResult(): array
    {
        $handler = (new EventTrackerDriverFactory)->getInstance(config('event-tracker.driver'));

        return $handler->getEventInsights($this->from, $this->to, $this->event, $this->userId);
    }

    protected function handleOutput(array $results): void
    {
        $this->output->title($results['title']);
        $this->output->text("Generated at:  {$results['generated_at']} ");
        $this->output->text("Between : {$results['period']['start']} & {$results['period']['end']}");
        $this->output->section("Top 3 events");
        $this->table(
            ["Event", 'Count'],
            array_map(fn($value, $key) => [$key, $value], $results['data']['top_3_events'], array_keys($results['data']['top_3_events'])),
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

        $this->output->section("Events Insights");
        $this->table(["Day", 'Event', 'Count'], $rows);
    }
}
