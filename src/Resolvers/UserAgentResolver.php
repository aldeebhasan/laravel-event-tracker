<?php

namespace Aldeebhasan\LaravelEventTracker\Resolvers;

use Aldeebhasan\LaravelEventTracker\Contracts\ResolveUI;
use Aldeebhasan\LaravelEventTracker\EventTracker;

class UserAgentResolver implements ResolveUI
{
    public static function resolve(EventTracker $tracker): string
    {
        return $tracker->preloadedResolverData['user_agent'] ?? (request()->userAgent() ?? '');
    }
}
