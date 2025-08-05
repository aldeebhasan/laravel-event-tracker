<?php

namespace Aldeebhasan\LaravelEventTracker\Trackers;

use Aldeebhasan\LaravelEventTracker\Contracts\TrackerUI;
use Aldeebhasan\LaravelEventTracker\Models\EventTracker;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DatabaseTracker extends AbstractTracker
{
    public function track(array $meta, string $event, array $context = []): void
    {
        $data = [
            'event' => $event,
            'date' => now()->toDateString(),
            'context' => $context,
            'ip_address' => $meta['ip_address'] ?? '0.0.0.0',
            'user_agent' => $meta['user_agent'] ?? '',
            'trackable_type' => !empty($meta['user']) ? get_class($meta['user']) : null,
            'trackable_id' => !empty($meta['user']) ? $meta['user']->getKey() : null,
            'tags' => '',
        ];
        $model = config('event-tracker.implementation', EventTracker::class);
        $model::create($data);
    }

    public function initialize(array $config): TrackerUI
    {
        return new self;
    }

    private function bseQuery(?string $from, ?string $to, ?string $event, string|int|null $userId): Builder
    {
        $model = config('event-tracker.implementation', EventTracker::class);

        return DB::connection((new $model)->getConnectionName())
            ->table((new $model)->getTable())
            ->when($from, fn(Builder $q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn(Builder $q) => $q->whereDate('date', '<=', $to))
            ->when($event, fn(Builder $q) => $q->where('event', $event))
            ->when($userId, fn(Builder $q) => $q->where('trackable_id', $userId));
    }

    private function getReportTitle(string $title, ?string $event, string|int|null $userId): string
    {
        $customTitle = $event ? " - By Event '$event'" : "";
        $customTitle .= $userId ? " - By User '$userId'" : "";

        return "{$title}{$customTitle}";
    }

    private function template(string $title, string $from, string $to, array $statistics): array
    {
        return [
            "title" => $title,
            "generated_at" => now()->toDateTimeString(),
            "period" => [
                "start" => $from,
                "end" => $to,
            ],
            "data" => $statistics,
        ];
    }

    public function getStatistic(string $from, string $to, ?string $event = null, string|int|null $userId = null): array
    {
        $statistics = $this->bseQuery($from, $to, $event, $userId)->select([
            'total_events' => DB::raw('count(event) as total_events'),
            'unique_events_count' => DB::raw('count(DISTINCT event ) as unique_events_count'),
            'total_users' => DB::raw('count(trackable_id) as total_users'),
            'unique_user_count' => DB::raw('count(DISTINCT trackable_id) as unique_user_count'),
        ])->first();

        $title = $this->getReportTitle("Global Events Statistic ", $event, $userId);

        return $this->template($title, $from, $to, (array)$statistics);
    }

    public function getEventInsights(string $from, string $to, ?string $event = null, string|int|null $userId = null): array
    {
        $statistics = $this->bseQuery($from, $to, $event, $userId)->select([
            'event',
            'total_events' => DB::raw('count(event) as total_events'),
        ])->groupBy('event')->orderByDesc('total_events')->limit(3)->get();

        $data['top_3_events'] = $statistics->mapWithKeys(fn(object $statistic) => [
            $statistic->event => $statistic->total_events,
        ])->toArray();

        $statistics = $this->bseQuery($from, $to, $event, $userId)->select([
            'date', 'event',
            'total_events' => DB::raw('count(event) as total_events'),
        ])->groupBy(['date', 'event'])->orderBy('date')->get();

        $data['by_days'] = $statistics->groupBy('date')->mapWithKeys(fn(Collection $collection, $group) => [
            $group => $collection->mapWithKeys(fn(object $statistic) => [
                $statistic->event => $statistic->total_events,
            ]),
        ])->toArray();

        $title = $this->getReportTitle("Events Insights", $event, $userId);

        return $this->template($title, $from, $to, $data);
    }

    public function getUserInsights(string $from, string $to, ?string $event = null, string|int|null $userId = null): array
    {
        $statistics = $this->bseQuery($from, $to, $event, $userId)->select([
            'trackable_id', 'trackable_type',
            'total_events' => DB::raw('count(event) as total_events'),
        ])->groupBy(['trackable_type', 'trackable_id'])->orderByDesc('total_events')->limit(3)->get();

        $classShortName = function (?string $class, string|int|null $key) {
            if (!$class) {
                return "Anonymous";
            }

            $reflect = new \ReflectionClass($class);
            $className = $reflect->getShortName();

            return sprintf("%s(#%s)", str($className)->singular()->title(), $key);
        };

        $data['top_3_users'] = $statistics->mapWithKeys(fn(object $statistic) => [
            $classShortName($statistic->trackable_type, $statistic->trackable_id) => $statistic->total_events,
        ])->toArray();

        $statistics = $this->bseQuery($from, $to, $event, $userId)->select([
            'date', 'trackable_id', 'trackable_type',
            'total_events' => DB::raw('count(event) as total_events'),
        ])->groupBy(['date', 'trackable_type', 'trackable_id'])->orderBy('date')->get();

        $data['by_days'] = $statistics->groupBy('date')->mapWithKeys(fn(Collection $collection, $group) => [
            $group => $collection->mapWithKeys(fn(object $statistic) => [
                $classShortName($statistic->trackable_type, $statistic->trackable_id) => $statistic->total_events,
            ]),
        ])->toArray();

        $title = $this->getReportTitle("User Insights", $event, $userId);

        return $this->template($title, $from, $to, $data);
    }

    public function getEventFrequency(string $from, string $to, ?string $event = null, string|int|null $userId = null): array
    {
        $statistics = $this->bseQuery($from, $to, $event, $userId)->select([
            'event',
            'total_events' => DB::raw('count(event) as total_events'),
        ])->groupBy('event')->orderByDesc('total_events')
            ->get()
            ->mapWithKeys(fn(object $statistic) => [
                $statistic->event => $statistic->total_events,
            ])->toArray();

        $title = $this->getReportTitle("Events Frequency ", $event, $userId);

        return $this->template($title, $from, $to, (array)$statistics);
    }
}
