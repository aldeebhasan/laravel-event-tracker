<?php

namespace Aldeebhasan\LaravelEventTracker\Tests;

use Aldeebhasan\LaravelEventTracker\EventTrackerServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'Aldeebhasan\\LaravelEventTracker\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        $this->loadMigrationsFrom(__DIR__ . '/Sample/database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            EventTrackerServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('event-tracker.enabled', true);
        config()->set('event-tracker.drivers.database.connection', 'sqlite');

        foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__ . '/../database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
        }
    }
}
