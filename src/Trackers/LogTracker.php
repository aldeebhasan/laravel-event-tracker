<?php

namespace Aldeebhasan\LaravelEventTracker\Trackers;

use Aldeebhasan\LaravelEventTracker\Contracts\TrackerUI;
use Aldeebhasan\LaravelEventTracker\Exceptions\TrackingException;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class LogTracker implements TrackerUI
{
    private LoggerInterface $channel;

    public function track(array $meta, string $event, array $context = []): void
    {
        $formattedContext = implode(', ', array_map(fn($key, $value) => "$key: $value", array_keys($context), array_values($context)));
        $formattedMeta = $this->getMeta($meta);

        $message = sprintf(
            "[%s] Event:[%s], Context:[%s], Meta:[%s]",
            now(), $event, $formattedContext, $formattedMeta
        );

        $this->channel->info($message);
    }

    private function getMeta(array $meta): string
    {
        $formattedMeta = [];
        if (!empty($meta['user'])) {
            $user = $meta['user'];
            $formattedMeta[] = "user: ($user->id, $user->name)";
        }
        foreach ($meta as $key => $value) {
            if (empty($value)) {
                continue;
            }

            if ($key === 'user') {
                $formattedMeta[] = "user: ($value->id, $value->name)";
            } else {
                $formattedMeta[] = "$key: $value";
            }
        }

        return implode(', ', $formattedMeta);
    }

    public function initialize(array $config): TrackerUI
    {
        throw_if(
            empty($config['driver']) || empty($config['path']),
            TrackingException::class,
            "Log tracker driver not specified"
        );

        $this->channel = Log::build([
            'driver' => $config['driver'],
            'path' => $config['path'],
            'level' => 'info',
            'replace_placeholders' => true,
        ]);

        return $this;
    }
}
