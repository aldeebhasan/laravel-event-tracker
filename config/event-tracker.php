<?php

// config for Hasan Deeb/LaravelEventTracker
return [
    'enabled' => env('TRACKING_ENABLED', false),

    'implementation' => Aldeebhasan\LaravelEventTracker\Models\EventTracker::class,

    'driver' => 'log',
    'drivers' => [
        'log' => [
            'driver' => 'single',
            'path' => storage_path('logs/packages/event-tracker/events.log'),
        ],
        'database' => [
            'table' => 'events',
            'connection' => 'mysql',
        ],
    ],

    'user' => [
        'morph_prefix' => 'user',
        'resolver' => Aldeebhasan\LaravelEventTracker\Resolvers\UserResolver::class,
    ],
    'resolvers' => [
        'ip_address' => Aldeebhasan\LaravelEventTracker\Resolvers\IpAddressResolver::class,
        'user_agent' => Aldeebhasan\LaravelEventTracker\Resolvers\UserAgentResolver::class,
    ],
];
