<?php

namespace Aldeebhasan\LaravelEventTracker\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Aldeebhasan\LaravelEventTracker\LaravelEventTracker
 */
class EventTracker extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Aldeebhasan\LaravelEventTracker\EventTracker::class;
    }
}
