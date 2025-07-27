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
            default => $this->handleCustomTracker($config),
        };

        return (new $className)->initialize($config);
    }

    /**
     * @throws TrackingException
     */
    private function handleCustomTracker(array &$config): string
    {
        if (empty($config['implementation'])) {
            throw new TrackingException('`implementation` config parameter is required for custom tracker');
        }

        if (!class_exists($config['implementation']) || !is_subclass_of($config['implementation'], TrackerUI::class)) {
            throw new TrackingException('`implementation` config parameter should be a valid class and implement the `TrackerUI` interface');
        }
        $implementation = $config['implementation'];
        unset($config['implementation']);

        return $implementation;
    }
}
