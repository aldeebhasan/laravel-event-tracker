<?php

namespace Aldeebhasan\LaravelEventTracker\Commands;

use Aldeebhasan\LaravelEventTracker\Factories\EventTrackerDriverFactory;

class EventFrequencyCommand extends AbstractCommand
{
    public $signature = 'event-tracker:frequency
                        {--from= : Start date (YYYY-MM-DD)}
                        {--to= : End date (YYYY-MM-DD)}
                        {--event= : Specific event name}
                        {--user_id= : Specific user id}';
    public $description = 'Show the event frequency bases on specific/all users';

    protected function getResult(): array
    {
        $handler = (new EventTrackerDriverFactory)->getInstance(config('event-tracker.driver'));

        return $handler->getEventFrequency($this->from, $this->to, $this->event, $this->userId);
    }

    protected function handleOutput(array $results): void
    {
        $this->output->title($results['title']);
        $this->output->text("Generated at:  {$results['generated_at']} ");
        $this->output->text("Between : {$results['period']['start']} & {$results['period']['end']}");
        $this->output->section("Event frequencies");
        $this->table(
            ["Event", 'Count'],
            array_map(fn($value, $key) => [$key, $value], $results['data'], array_keys($results['data'])),
        );
    }
}
