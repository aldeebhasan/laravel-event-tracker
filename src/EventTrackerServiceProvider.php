<?php

namespace Aldeebhasan\LaravelEventTracker;

use Carbon\Laravel\ServiceProvider;

class EventTrackerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/event-tracker.php' => config_path('event-tracker.php'),
        ], 'tracker-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path("migrations"),
        ], 'tracker-migrations');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/event-tracker'),
        ], 'tracker-views');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/event-tracker.php', 'event-tracker');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'event-tracker');
//        $this->loadRoutesFrom(__DIR__ . '/../src/Http/Routes/web.php');
        $this->commands([

        ]);
    }
}
