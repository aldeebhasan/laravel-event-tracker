<?php

namespace Aldeebhasan\LaravelEventTracker\Contracts;

interface TrackerUI
{
    /**
     * @param array<string,mixed> $context
     */
    public function track(string $event, array $context = []): void;
}
