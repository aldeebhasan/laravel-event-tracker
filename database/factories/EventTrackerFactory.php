<?php

namespace Aldeebhasan\LaravelEventTracker\Database\Factories;

use Aldeebhasan\LaravelEventTracker\EventTracker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class EventTrackerFactory extends Factory
{
    /** @phpstan-ignore property.defaultValue */
    protected $model = EventTracker::class;

    public function definition(): array
    {
        return [
            'event' => $this->faker->word(),
            'context' => [],
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'tags' => '',
        ];
    }

    public function withTrackable(Model $trackable): self
    {
        return $this->state([
            'trackable_type' => get_class($trackable),
            'trackable_id' => $trackable->getKey(),
        ]);
    }
}
