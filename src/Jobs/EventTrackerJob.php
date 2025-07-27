<?php

namespace Aldeebhasan\LaravelEventTracker\Jobs;

use Aldeebhasan\LaravelEventTracker\Factories\EventTrackerDriverFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EventTrackerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $driver,
        private readonly array $meta,
        private readonly string $event,
        private readonly array $context = []
    ) {
        $this->connection = config('event-tracker.queue.connection', 'sync');
        $this->queue = config('event-tracker.queue.queue');
    }

    public function handle(): void
    {
        $tracker = (new EventTrackerDriverFactory)->getInstance($this->driver);
        $tracker->track($this->meta, $this->event, $this->context);
    }
}
