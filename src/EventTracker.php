<?php

namespace Aldeebhasan\LaravelEventTracker;

use Aldeebhasan\LaravelEventTracker\Contracts\ResolveUI;
use Aldeebhasan\LaravelEventTracker\Contracts\TrackerUI;
use Aldeebhasan\LaravelEventTracker\Exceptions\TrackingException;
use Aldeebhasan\LaravelEventTracker\Trackers\LogTracker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class EventTracker
{
    private ?TrackerUI $tracker;
    private array $preloadedResolverData = [];

    public function getPreloadedResolverData(): array
    {
        return $this->preloadedResolverData;
    }

    public function tracker(string $className = ''): self
    {
        $className = $className ?: config('event-tracker.tracker');

        if (!class_exists($className)) {
            $className = LogTracker::class;
        }
        $this->tracker = new $className;
        $this->preloadResolverData();

        return $this;
    }

    public function user(Authenticatable|Model $user): self
    {
        $this->preloadedResolverData['user'] = $user;

        return $this;
    }

    public function track(string $event, array $context = []): void
    {
        $enabled = config('event-tracker.enabled', false);
        if (!$enabled) {
            return;
        }

        if (!$this->tracker) {
            $this->tracker();
        }

        $this->tracker->track($event, $context);
    }

    public function preloadResolverData(): self
    {
        $this->preloadedResolverData = $this->runResolvers();

        $user = $this->resolveUser();
        if (!empty($user)) {
            $this->preloadedResolverData['user'] = $user;
        }

        return $this;
    }

    protected function resolveUser(): mixed
    {
        if (!empty($this->preloadedResolverData['user'] ?? null)) {
            return $this->preloadedResolverData['user'];
        }

        $userResolver = config('event-tracker.user.resolver');

        if (!is_subclass_of($userResolver, ResolveUI::class)) {
            throw new TrackingException('Invalid UserResolver implementation');
        }

        return call_user_func([$userResolver, 'resolve'], $this);
    }

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
