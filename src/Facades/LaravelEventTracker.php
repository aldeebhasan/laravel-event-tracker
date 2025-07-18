<?php

namespace Aldeebhasan\LaravelEventTracker\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hasan Deeb\LaravelEventTracker\LaravelEventTracker
 */
class LaravelEventTracker extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Aldeebhasan\LaravelEventTracker\LaravelEventTracker::class;
    }
}
