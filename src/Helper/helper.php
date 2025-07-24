<?php

if (!function_exists('tracker')) {
    function tracker(string $driver = ''): Aldeebhasan\LaravelEventTracker\EventTracker
    {
        return Aldeebhasan\LaravelEventTracker\Facades\EventTracker::tracker($driver);
    }
}

if (!function_exists('track')) {
    function track(string $event, array $context = []): void
    {
        tracker()->track($event, $context);
    }
}
