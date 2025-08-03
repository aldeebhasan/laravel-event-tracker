<?php

namespace Aldeebhasan\LaravelEventTracker\Facades;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Aldeebhasan\LaravelEventTracker\EventTracker driver(string $driver = '')
 * @method static \Aldeebhasan\LaravelEventTracker\EventTracker user(Authenticatable|Model $user)
 * @method static void track_event(string $event, array $context = [])
 *
 * @see \Aldeebhasan\LaravelEventTracker\LaravelEventTracker
 */
class EventTracker extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Aldeebhasan\LaravelEventTracker\EventTracker::class;
    }
}
