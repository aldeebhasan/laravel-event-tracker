<?php

namespace Aldeebhasan\LaravelEventTracker\Trackers;

use Aldeebhasan\LaravelEventTracker\Contracts\TrackerUI;

class LogTracker implements TrackerUI
{

    /**
     * @param array<string,mixed> $context
     */
    public function track(string $event, array $context = []): void
    {
        // TODO: Implement track() method.
    }
}
