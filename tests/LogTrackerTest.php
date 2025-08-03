<?php

beforeEach(function () {
    $path = config('event-tracker.drivers.log.path');
    if (file_exists($path)) {
        unlink($path);
    }
});

it('can track event', function () {
    tracker('log')->track_event('action.created');
    $path = config('event-tracker.drivers.log.path');

    $exist = file_exists($path);
    expect($exist)->toBeTrue();

    $lastLog = last(file($path));
    expect($lastLog)->toContain("action.created");
});

it('can track event on queue (sync mode)', function () {
    config()->set('event-tracker.queue.enabled', true);
    config()->set('event-tracker.queue.connection', 'sync');

    tracker('log')->track_event('action.created');
    $path = config('event-tracker.drivers.log.path');

    $exist = file_exists($path);
    expect($exist)->toBeTrue();

    $lastLog = last(file($path));
    expect($lastLog)->toContain("action.created");
});

it('can track event on queue (async mode)', function () {
    Illuminate\Support\Facades\Queue::fake();
    config()->set('event-tracker.queue.enabled', true);
    config()->set('event-tracker.queue.connection', 'database');

    tracker('log')->track_event('action.created');
    Illuminate\Support\Facades\Queue::assertPushed(
        Aldeebhasan\LaravelEventTracker\Jobs\EventTrackerJob::class,
        function (Aldeebhasan\LaravelEventTracker\Jobs\EventTrackerJob $job) {
            $reflection = new ReflectionClass($job);

            if ($reflection->getProperty('driver')->getValue($job) !== 'log') {
                return false;
            }
            if ($reflection->getProperty('event')->getValue($job) !== 'action.created') {
                return false;
            }

            return true;
        }

    );
});

it('can retrieve accurate statistics', function () {
    $this->artisan('event-tracker:statistics')->assertFailed();
});

it('can retrieve accurate event insights', function () {
    $this->artisan('event-tracker:event-insights')->assertFailed();
});
it('can retrieve accurate user insights', function () {
    $this->artisan('event-tracker:user-insights')->assertFailed();
});
