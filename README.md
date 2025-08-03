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
