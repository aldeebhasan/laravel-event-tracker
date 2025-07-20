<?php

// config for Hasan Deeb/LaravelEventTracker
return [
    'enabled' => env('TRACKING_ENABLED', false),
    'tracker' => Aldeebhasan\LaravelEventTracker\Trackers\LogTracker::class,

    'user' => [
        'morph_prefix' => 'user',
        'resolver' => Aldeebhasan\LaravelEventTracker\Resolvers\UserResolver::class,
    ],
    'resolvers' => [
        'ip_address' => Aldeebhasan\LaravelEventTracker\Resolvers\IpAddressResolver::class,
        'user_agent' => Aldeebhasan\LaravelEventTracker\Resolvers\UserAgentResolver::class,
    ],
];
