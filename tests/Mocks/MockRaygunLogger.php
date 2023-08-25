<?php

namespace LlewellynKevin\RaygunLogger\Tests\Mocks;

use Illuminate\Support\Collection;
use LlewellynKevin\RaygunLogger\Contracts\RaygunLoggerContract;
use Throwable;

class MockRaygunLogger implements RaygunLoggerContract
{
    public Collection $loggedExceptions;
    public Collection $levels;

    public function __construct(
        public RaygunLoggerContract $appLogger,
    ) {
        $this->loggedExceptions = collect([]);
        $this->levels = collect([]);
    }

    public function handle(Throwable $exception, array $customData = [])
    {
        $this->loggedExceptions->push($exception);
    }

    public function level(string $type, string|int $level): self
    {
        $this->levels->push([
            'type' => $type,
            'level' => $level,
        ]);

        $this->appLogger->level($type, $level);
        return $this;
    }

    public function hasLoggedException(?Throwable $exception = null): bool
    {
        if (is_null($exception)) return !$this->loggedExceptions->isEmpty();

        return $this->loggedExceptions->search($exception) !== false;
    }

    public function hasAssignedLevel(?string $exception = null): bool
    {
        if (is_null($exception)) return !$this->levels->isEmpty();

        return $this->levels->search(
            fn (array $level) => $level['type'] === $exception,
        ) !== false;
    }
}
