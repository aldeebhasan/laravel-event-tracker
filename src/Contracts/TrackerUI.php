<?php

namespace Aldeebhasan\LaravelEventTracker\Contracts;

interface TrackerUI
{
    /**
     * @param array<string,mixed> $config
     */
    public function initialize(array $config): self;

    /**
     * @param array{user: mixed, ip_address: string,user_agent:string} $meta
     * @param array<string,mixed> $context
     */
    public function track(array $meta, string $event, array $context = []): void;
}
