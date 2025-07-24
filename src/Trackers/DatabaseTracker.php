<?php

namespace Aldeebhasan\LaravelEventTracker\Trackers;

use Aldeebhasan\LaravelEventTracker\Contracts\TrackerUI;

class DatabaseTracker implements TrackerUI
{
    public function track(array $meta, string $event, array $context = []): void
    {
        // TODO: Implement track() method.
    }

    public function initialize(array $config): TrackerUI
    {
        return new self;
    }
}
