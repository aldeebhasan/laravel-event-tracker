<?php

namespace Aldeebhasan\LaravelEventTracker\Resolvers;

use Aldeebhasan\LaravelEventTracker\Contracts\ResolveUI;
use Aldeebhasan\LaravelEventTracker\EventTracker;

class IpAddressResolver implements ResolveUI
{
    public static function resolve(EventTracker $tracker): string
    {
        return $tracker->getPreloadedResolverData()['ip_address'] ?? (request()->ip() ?? '');
    }
}
