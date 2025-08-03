<?php

namespace Aldeebhasan\LaravelEventTracker;

use Aldeebhasan\LaravelEventTracker\Contracts\ResolveUI;
use Aldeebhasan\LaravelEventTracker\Exceptions\TrackingException;
use Aldeebhasan\LaravelEventTracker\Factories\EventTrackerDriverFactory;
use Aldeebhasan\LaravelEventTracker\Jobs\EventTrackerJob;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Conditionable;

class EventTracker
{
    use Conditionable;

    private string $driver;
    private Authenticatable|Model|null $user = null;

    /**
     * @var array<string,mixed>
     */
    public array $preloadedResolverData = [];

    public function __construct()
    {
        $this->driver = config('event-tracker.driver');
    }

    /**
     * @throws TrackingException
     */
    public function driver(string $driver = ''): self
    {
        $this->driver = $driver ?: config('event-tracker.driver');

        return $this;
    }

    public function user(Authenticatable|Model $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param array<string,mixed> $context
     *
     * @throws TrackingException
     */
    public function track_event(string $event, array $context = []): void
    {
        $enabled = config('event-tracker.enabled', false);
        if (!$enabled) {
            return;
        }

        $this->preloadResolverData();

        if (config('event-tracker.queue.enabled', false)) {
            dispatch(new EventTrackerJob($this->driver, $this->preloadedResolverData, $event, $context));
        } else {
            $tracker = (new EventTrackerDriverFactory)->getInstance($this->driver);
            $tracker->track($this->preloadedResolverData, $event, $context);
        }
    }

    /**
     * @throws TrackingException
     */
    private function preloadResolverData(): void
    {
        $this->preloadedResolverData = $this->runResolvers();

        $user = $this->resolveUser();
        if (!empty($user)) {
            $this->preloadedResolverData['user'] = $user;
        }
    }

    /**
     * @throws TrackingException
     */
    private function resolveUser(): mixed
    {
        if (!empty($this->preloadedResolverData['user'] ?? null)) {
            return $this->preloadedResolverData['user'];
        }

        if (!empty($this->user)) {
            return $this->user;
        }

        $userResolver = config('event-tracker.user.resolver');

        if (!is_subclass_of($userResolver, ResolveUI::class)) {
            throw new TrackingException('Invalid UserResolver implementation');
        }

        return call_user_func([$userResolver, 'resolve'], $this);
    }

    /**
     * @return array<string,mixed>
     *
     * @throws TrackingException
     */
    private function runResolvers(): array
    {
        $resolved = [];
        $resolvers = config('event-tracker.resolvers', []);

        foreach ($resolvers as $name => $implementation) {
            if (empty($implementation)) {
                continue;
            }

            if (!is_subclass_of($implementation, ResolveUI::class)) {
                throw new TrackingException('Invalid Resolver implementation for: ' . $name);
            }
            $resolved[$name] = call_user_func([$implementation, 'resolve'], $this);
        }

        return $resolved;
    }
}
