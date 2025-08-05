# A privacy-first Laravel package for tracking conversions, events, and user journeys—100% locally stored. No third-party APIs, no external tracking.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/aldeebhasan/laravel-event-tracker.svg?style=flat-square)](https://packagist.org/packages/aldeebhasan/laravel-event-tracker)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/aldeebhasan/laravel-event-tracker/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/aldeebhasan/laravel-event-tracker/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/aldeebhasan/laravel-event-tracker/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/aldeebhasan/laravel-event-tracker/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amaster)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/04af7fd269044247b9d5330e0b7e56a2)](https://app.codacy.com/gh/aldeebhasan/laravel-event-tracker/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
[![Total Downloads](https://img.shields.io/packagist/dt/aldeebhasan/laravel-event-tracker.svg?style=flat-square)](https://packagist.org/packages/aldeebhasan/laravel-event-tracker)

A simple and flexible Laravel package for tracking events in your application. The laravel-event-tracker package provides an intuitive helper function to log events with minimal setup, abstracting the
complexity of event storage and retrieval. It supports multiple drivers (e.g., database, log) and includes Artisan commands to retrieve insightful statistics about your events.

## Installation

You can install the package via composer:

```bash
composer require aldeebhasan/laravel-event-tracker
```

You need first to publish and run the migrations with:

```bash
php artisan vendor:publish --tag="event-tracker-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="event-tracker-config"
```

## Usage

### Track events

After the configuration of the target driver you want to use in the config file (log by default),
you can start track your users with the following helpers

```php
track_event(event:"event name",context: [],user: auth()->user())
```

if you want to use different driver at run time, you can use the tracker helper to configure it.

```php
tracker('database')->track_event(event:"event name",context: [],user: auth()->user())
```

Alternately, you can use the package facade to call all the event tracker manager functions

```php
\Aldeebhasan\LaravelEventTracker\Facades\EventTracker::driver()->track_event("event name");
```
### View Statistics & Insights

We have four available commands that can tell you the whole story about your events:
- <b>event-tracker:frequency</b> : Show the event frequency bases on specific/all users.
- <b>event-tracker:event-insights</b> : Show the event insights for specific/all events within a specific period of time.
- <b>event-tracker:statistics</b> : Show general statistics about the events and users.
- <b>event-tracker:user-insights</b> : Show the user insights within a specific period of time.

all these commands has 4 input options
```php
php artisan event-tracker:command --from=   //  Start date (YYYY-MM-DD) and by default is yesterday
                                  --to=     //  End date (YYYY-MM-DD) and by default is today
                                  --event=  //  Specific event name to filter on
                                  --user_id=// Specific user id to filter on     
```

## Advanced Setup

### Custom Tracker
After publishing of the config, you have the ability to change the default trackers implementation, or add your custom tracker.
to configure your custom tracker you can define it in the config file as follow:
```php
    'drivers' => [
        /*'database' => [
            'table' => 'events',
            'connection' => 'mysql',
        ],*/
         'custom' => [
            'implementation' => YourProject\Trackers\CustomTracker::class,
            'api-key' => '***********',
            'project' => '***********',
        ],
    ],
```
Then you can use the tracker as follow 
```php
  tracker('custom')->track_event('action.created');
```
> [!IMPORTANT]
> The `CustomTracker` should implement the `TrackerUI` interface

### Additional Resolvers

In the config file you have a resolver array that contain all the resolver applied on the incoming request to extract data from it.
If you want to extract extra info from the current request you can create a new resolver class and add it to the resolver list.

As example:
let create a new resolver to extract the host from the request:
```php
class HostResolver implements ResolveUI
{
    public static function resolve(EventTracker $tracker): string
    {
        return $tracker->preloadedResolverData['host'] ?? (request()->getHost() ?? '');
    }
}
```

> [!IMPORTANT]
> The new resolver should implement the `ResolveUI` interface

The new resolver data will be passed within the $meta param within the `track` function in all of the tracker implementation. 

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Hasan Deeb](https://github.com/aldeebhasan)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
