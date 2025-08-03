<?php

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

if (!function_exists('tracker')) {
    function tracker(string $driver = ''): Aldeebhasan\LaravelEventTracker\EventTracker
    {
        return Aldeebhasan\LaravelEventTracker\Facades\EventTracker::driver($driver);
    }
}

if (!function_exists('track_event')) {
    function track_event(string $event, array $context = [], Authenticatable|Model|null $user = null): void
    {
        tracker()->when($user, fn($tracker) => $tracker->user($user))->track_event($event, $context);
    }
}
