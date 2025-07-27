<?php

namespace Aldeebhasan\LaravelEventTracker;

use Carbon\Laravel\ServiceProvider;

class EventTrackerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/event-tracker.php' => config_path('event-tracker.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/event_tracker.stub' => database_path(
                sprintf('migrations/%s_create_event_tracker_table.php', date('Y_m_d_His'))
            ),
        ], 'migrations');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/event-tracker.php', 'event-tracker');
        $this->commands([

        ]);
    }
}
