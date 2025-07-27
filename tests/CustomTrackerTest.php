<?php

beforeEach(function () {
    $path = storage_path('logs/packages/event-tracker/custom_events.log');

    $drivers = config('event-tracker.drivers');
    $drivers['custom'] = [
        'implementation' => Aldeebhasan\LaravelEventTracker\Trackers\LogTracker::class,
        'driver' => 'single',
        'path' => $path,
    ];
    config()->set('event-tracker.drivers', $drivers);
    if (file_exists($path)) {
        unlink($path);
    }
});

it('can track event (Custom)', function () {
    tracker('custom')->track('action.created');
    $path = config('event-tracker.drivers.custom.path');

    $exist = file_exists($path);
    expect($exist)->toBeTrue();

    $lastLog = last(file($path));
    expect($lastLog)->toContain("action.created");
});

it('can track event on queue (sync mode)', function () {
    config()->set('event-tracker.queue.enabled', true);
    config()->set('event-tracker.queue.connection', 'sync');

    tracker('custom')->track('action.created');
    $path = config('event-tracker.drivers.custom.path');

    $exist = file_exists($path);
    expect($exist)->toBeTrue();

    $lastLog = last(file($path));
    expect($lastLog)->toContain("action.created");
});

it('can track event on queue (async mode)', function () {
    Illuminate\Support\Facades\Queue::fake();
    config()->set('event-tracker.queue.enabled', true);
    config()->set('event-tracker.queue.connection', 'database');

    tracker('custom')->track('action.created');
    Illuminate\Support\Facades\Queue::assertPushed(
        Aldeebhasan\LaravelEventTracker\Jobs\EventTrackerJob::class,
        function (Aldeebhasan\LaravelEventTracker\Jobs\EventTrackerJob $job) {
            $reflection = new ReflectionClass($job);

            if ($reflection->getProperty('driver')->getValue($job) !== 'custom') {
                return false;
            }
            if ($reflection->getProperty('event')->getValue($job) !== 'action.created') {
                return false;
            }

            return true;
        }

    );
});
