<?php

namespace Aldeebhasan\LaravelEventTracker\Contracts;

use Aldeebhasan\LaravelEventTracker\EventTracker;

interface ResolveUI
{
    public static function resolve(EventTracker $tracker): mixed;
}
