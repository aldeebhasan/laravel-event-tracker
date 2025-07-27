<?php

namespace Aldeebhasan\LaravelEventTracker\Factories;

use Aldeebhasan\LaravelEventTracker\Contracts\TrackerUI;
use Aldeebhasan\LaravelEventTracker\Exceptions\TrackingException;
use Aldeebhasan\LaravelEventTracker\Trackers\DatabaseTracker;
use Aldeebhasan\LaravelEventTracker\Trackers\LogTracker;

class EventTrackerDriverFactory
{
    /**
     * @throws TrackingException
     */
    public function getInstance(string $driver): TrackerUI
    {
        $config = config('event-tracker.drivers.' . $driver);
        if (!$config) {
            throw new TrackingException('Invalid Tracker implementation');
        }

        $className = match ($driver) {
            'log' => LogTracker::class,
            'database' => DatabaseTracker::class,
            default => throw new TrackingException('Unknown database driver: ' . $driver),
        };

        return (new $className)->initialize($config);
    }
}
