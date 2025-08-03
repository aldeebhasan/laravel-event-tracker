<?php

namespace Aldeebhasan\LaravelEventTracker\Commands;

use Aldeebhasan\LaravelEventTracker\Exceptions\TrackingException;
use Carbon\Carbon;
use Illuminate\Console\Command;

abstract class AbstractCommand extends Command
{
    protected ?string $from;
    protected ?string $to;
    protected ?string $event;
    protected ?string $userId;

    /**
     * @throws TrackingException
     */
    public function handle(): int
    {
        $this->handleOptions();
        try {
            $results = $this->getResult();
        } catch (TrackingException $e) {
            $this->output->error($e->getMessage());

            return self::FAILURE;
        }

        $this->handleOutput($results);

        return self::SUCCESS;
    }

    protected function handleOptions(): void
    {
        $this->userId = $this->option('user_id');
        $this->event = $this->option('event');
        $this->from = $this->option('from');
        $this->to = $this->option('to');

        if ($this->from && !$this->to) {
            $this->to = Carbon::parse($this->from)->addDay();
        }

        if (!$this->from && $this->to) {
            $this->from = Carbon::parse($this->to)->subDay();
        }

        $this->from = Carbon::parse($this->from)->toDateString();
        $this->to = Carbon::parse($this->to)->toDateString();
    }

    /**
     * @throws TrackingException
     */
    abstract protected function getResult(): array;

    abstract protected function handleOutput(array $results): void;
}
