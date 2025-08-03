<?php

namespace Aldeebhasan\LaravelEventTracker\Trackers;

use Aldeebhasan\LaravelEventTracker\Contracts\TrackerUI;
use Aldeebhasan\LaravelEventTracker\Exceptions\TrackingException;

abstract class AbstractTracker implements TrackerUI
{
    /**
     * @throws TrackingException
     */
    public function getStatistic(string $from, string $to, ?string $event = null, string|int|null $userId = null): array
    {
        throw new TrackingException("Statistic are not supported for current driver right now!!");
    }

    public function getEventInsights(string $from, string $to, ?string $event = null, string|int|null $userId = null): array
    {
        throw new TrackingException("Event Insights are not supported for current driver right now!!");
    }

    public function getUserInsights(string $from, string $to, ?string $event = null, string|int|null $userId = null): array
    {
        throw new TrackingException("User Insights are not supported for current driver right now!!");
    }
}
