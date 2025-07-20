<?php

namespace Aldeebhasan\LaravelEventTracker\Contracts;

interface TrackerUI
{
    public function track(string $event, array $context = []): void;
}
