<?php

namespace Aldeebhasan\LaravelEventTracker\Trackers;

use Aldeebhasan\LaravelEventTracker\Contracts\TrackerUI;
use Aldeebhasan\LaravelEventTracker\Exceptions\TrackingException;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class LogTracker extends AbstractTracker
{
    private LoggerInterface $channel;

    public function track(array $meta, string $event, array $context = []): void
    {
        $message = sprintf(
            "Event:[%s], Context:%s, Meta:%s",
            $event,
            json_encode($context, JSON_UNESCAPED_UNICODE),
            json_encode($this->getMeta($meta), JSON_UNESCAPED_UNICODE),
        );

        $this->channel->info($message);
    }

    private function getMeta(array $meta): array
    {
        $formattedMeta = [];

        foreach ($meta as $key => $value) {
            if (empty($value)) {
                continue;
            }

            if ($key === 'user') {
                $formattedMeta['user_id'] = (string)$value->getKey();
                $formattedMeta['user_name'] = (string)$value->name;
            } else {
                $formattedMeta[$key] = (string)$value;
            }
        }

        return $formattedMeta;
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
