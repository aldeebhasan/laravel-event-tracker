<?php

namespace Aldeebhasan\LaravelEventTracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EventTracker extends Model
{
    protected $guarded = [];
    protected $casts = [
        'context' => 'array',
    ];

    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getConnectionName(): string
    {
        return config('event-tracker.drivers.database.connection');
    }

    public function getTable(): string
    {
        return config('event-tracker.drivers.database.table', 'events');
    }
}
