<?php

it('can track event (DB)', function () {
    tracker('database')->track('action.created');
    $count  = \Aldeebhasan\LaravelEventTracker\Models\EventTracker::count();

    expect($count)->toBe(1);
});

it('can track event on queue (sync mode) (DB)', function () {
    config()->set('event-tracker.queue.enabled', true);
    config()->set('event-tracker.queue.connection', 'sync');

    tracker('database')->track('action.created');
    $count  = \Aldeebhasan\LaravelEventTracker\Models\EventTracker::count();

    expect($count)->toBe(1);
});

it('can track event on queue (async mode) (DB)', function () {
    \Illuminate\Support\Facades\Queue::fake();
    config()->set('event-tracker.queue.enabled', true);
    config()->set('event-tracker.queue.connection', 'database');

    tracker('database')->track('action.created');
    \Illuminate\Support\Facades\Queue::assertPushed(
        \Aldeebhasan\LaravelEventTracker\Jobs\EventTrackerJob::class,
        function (\Aldeebhasan\LaravelEventTracker\Jobs\EventTrackerJob $job) {
            $reflection = new \ReflectionClass($job);

            if ($reflection->getProperty('driver')->getValue($job) !== 'database') {
                return false;
            }
            if ($reflection->getProperty('event')->getValue($job) !== 'action.created') {
                return false;
            }
            return true;
        }

    );
});
