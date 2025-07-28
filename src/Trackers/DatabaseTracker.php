<?php

namespace Aldeebhasan\LaravelEventTracker\Trackers;

use Aldeebhasan\LaravelEventTracker\Contracts\TrackerUI;
use Aldeebhasan\LaravelEventTracker\Models\EventTracker;

class DatabaseTracker implements TrackerUI
{
    public function track(array $meta, string $event, array $context = []): void
    {
        $data = [
            'event' => $event,
            'date' => now()->toDateString(),
            'context' => $context,
            'ip_address' => $meta['ip_address'] ?? '0.0.0.0',
            'user_agent' => $meta['user_agent'] ?? '',
            'trackable_type' => !empty($meta['user']) ? get_class($meta['user']) : null,
            'trackable_id' => !empty($meta['user']) ? $meta['user']->getKey() : null,
            'tags' => '',
        ];
        EventTracker::create($data);
    }

    public function initialize(array $config): TrackerUI
    {
        return new self;
    }

    public function statistic(?string $event, ?string $from, ?string $to): array
    {

        return [];
    }
}
