<?php

use Aldeebhasan\LaravelEventTracker\Factories\EventTrackerDriverFactory;

it('can track event (DB)', function () {
    tracker('database')->track('action.created');
    $count = Aldeebhasan\LaravelEventTracker\Models\EventTracker::count();

    expect($count)->toBe(1);
});

it('can track event on queue (sync mode) (DB)', function () {
    config()->set('event-tracker.queue.enabled', true);
    config()->set('event-tracker.queue.connection', 'sync');

    tracker('database')->track('action.created');
    $count = Aldeebhasan\LaravelEventTracker\Models\EventTracker::count();

    expect($count)->toBe(1);
});

it('can track event on queue (async mode) (DB)', function () {
    Illuminate\Support\Facades\Queue::fake();
    config()->set('event-tracker.queue.enabled', true);
    config()->set('event-tracker.queue.connection', 'database');

    tracker('database')->track('action.created');
    Illuminate\Support\Facades\Queue::assertPushed(
        Aldeebhasan\LaravelEventTracker\Jobs\EventTrackerJob::class,
        function (Aldeebhasan\LaravelEventTracker\Jobs\EventTrackerJob $job) {
            $reflection = new ReflectionClass($job);

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

it('can run package commands', function () {
    config()->set('event-tracker.driver', 'database');
    $user = Aldeebhasan\LaravelEventTracker\Tests\Sample\App\Models\User::factory()->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory()->withTrackable($user)->count(50)->create();

    $this->artisan('event-tracker:statistics')->assertExitCode(0);
    $this->artisan('event-tracker:event-insights')->assertExitCode(0);
    $this->artisan('event-tracker:user-insights')->assertExitCode(0);
});

it('can get accurate statistics', function () {
    config()->set('event-tracker.driver', 'database');
    $users = Aldeebhasan\LaravelEventTracker\Tests\Sample\App\Models\User::factory()->count(3)->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory()->withTrackable($users[0])->count(50)->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory()->withTrackable($users[1])->count(50)->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory()->withTrackable($users[2])->count(50)->create();

    $handler = (new EventTrackerDriverFactory)->getInstance(config('event-tracker.driver'));
    $results = $handler->getStatistic(now()->toDateString(), now()->addYear()->toDateString());

    expect($results['data'])->toBe([
        "total_events" => 150,
        "unique_events_count" => 5,
        "total_users" => 150,
        "unique_user_count" => 3,
    ]);
});

it('can get accurate statistics by event', function () {
    config()->set('event-tracker.driver', 'database');
    $users = Aldeebhasan\LaravelEventTracker\Tests\Sample\App\Models\User::factory()->count(2)->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory(['event' => 'login.success'])->withTrackable($users[0])->count(50)->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory(['event' => 'login.success'])->withTrackable($users[1])->count(50)->create();

    $handler = (new EventTrackerDriverFactory)->getInstance(config('event-tracker.driver'));
    $results = $handler->getStatistic(now()->toDateString(), now()->addYear()->toDateString(), 'login.success');

    expect($results['data'])->toBe([
        "total_events" => 100,
        "unique_events_count" => 1,
        "total_users" => 100,
        "unique_user_count" => 2,
    ]);
});

it('can get accurate statistics by user', function () {
    config()->set('event-tracker.driver', 'database');
    $users = Aldeebhasan\LaravelEventTracker\Tests\Sample\App\Models\User::factory()->count(2)->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory()->withTrackable($users[0])->count(50)->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory()->withTrackable($users[1])->count(50)->create();

    $handler = (new EventTrackerDriverFactory)->getInstance(config('event-tracker.driver'));
    $results = $handler->getStatistic(now()->toDateString(), now()->addYear()->toDateString(), userId: $users->first()->id);

    expect($results['data'])->toBe([
        "total_events" => 50,
        "unique_events_count" => 5,
        "total_users" => 50,
        "unique_user_count" => 1,
    ]);
});

it('can get accurate event insights', function () {
    config()->set('event-tracker.driver', 'database');
    $users = Aldeebhasan\LaravelEventTracker\Tests\Sample\App\Models\User::factory()->count(3)->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory()->withTrackable($users[0])->count(50)->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory()->withTrackable($users[1])->count(50)->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory()->withTrackable($users[2])->count(50)->create();

    $handler = (new EventTrackerDriverFactory)->getInstance(config('event-tracker.driver'));
    $results = $handler->getEventInsights(now()->toDateString(), now()->addYear()->toDateString());

    expect($results['data'])->toHaveKey('top_3_events');
});

it('can get accurate user insights', function () {
    config()->set('event-tracker.driver', 'database');
    $users = Aldeebhasan\LaravelEventTracker\Tests\Sample\App\Models\User::factory()->count(3)->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory()->withTrackable($users[0])->count(50)->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory()->withTrackable($users[1])->count(50)->create();
    Aldeebhasan\LaravelEventTracker\Models\EventTracker::factory()->withTrackable($users[2])->count(50)->create();

    $handler = (new EventTrackerDriverFactory)->getInstance(config('event-tracker.driver'));
    $results = $handler->getUserInsights(now()->toDateString(), now()->addYear()->toDateString());

    expect($results['data'])->toHaveKey('top_3_users');
});
