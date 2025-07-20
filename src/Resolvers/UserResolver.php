<?php

namespace Aldeebhasan\LaravelEventTracker\Resolvers;

use Aldeebhasan\LaravelEventTracker\Contracts\ResolveUI;
use Aldeebhasan\LaravelEventTracker\EventTracker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class UserResolver implements ResolveUI
{
    public static function resolve(EventTracker $tracker): Authenticatable|Model|null
    {
        return auth()->user();
    }
}
