<?php

namespace Aldeebhasan\LaravelEventTracker\Trackers;

use Aldeebhasan\LaravelEventTracker\Contracts\TrackerUI;

class DatabaseTracker implements TrackerUI
{
    public function track(string $event, array $context = []): void
    {
        // TODO: Implement track() method.
    }
}
