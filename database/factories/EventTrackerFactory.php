<?php

namespace Aldeebhasan\LaravelEventTracker\Database\Factories;

use Aldeebhasan\LaravelEventTracker\Models\EventTracker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class EventTrackerFactory extends Factory
{
    protected $model = EventTracker::class;

    public function definition(): array
    {
        $events = ['login.success', 'cart.add', 'cart.remove', 'cart.empty', 'checkout.start'];

        return [
            'event' => $this->faker->randomElement($events),
            'context' => [],
            'date' => now()->subHours($this->faker->numberBetween(int2: 200))->toDateString(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'tags' => '',
            'trackable_type' => User::class,
            'trackable_id' => 0,
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
