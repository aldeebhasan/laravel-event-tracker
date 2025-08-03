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

    public function getStatistic(string $from, string $to, ?string $event = null, string|int|null $userId = null): array;

    public function getEventInsights(string $from, string $to, ?string $event = null, string|int|null $userId = null): array;

    public function getUserInsights(string $from, string $to, ?string $event = null, string|int|null $userId = null): array;

    public function getEventFrequency(string $from, string $to, ?string $event = null, string|int|null $userId = null): array;
}
