<?php

namespace LlewellynKevin\RaygunLogger;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use LlewellynKevin\RaygunLogger\Contracts\RaygunClientContract;
use LlewellynKevin\RaygunLogger\Contracts\RaygunLoggerContract;
use LlewellynKevin\RaygunLogger\Contracts\RaygunMetaServiceContract;
use Psr\Log\LogLevel;
use Throwable;

class RaygunLogger implements RaygunLoggerContract
{
    protected array $levels = [];

    public function __construct(
        public RaygunMetaServiceContract $metaService,
        public RaygunClientContract $client,
    ) {
    }

    public function handle(Throwable $exception, array $customData = [])
    {
        if (!App::environment(config('raygun-logger.environments'))) {
            return false;
        }

        if (!$this->metaService->shouldLog($this->getExceptionLevel($exception), $exception)) {
            return false;
        }

        $this->client->SetUser(...$this->metaService->getUser($exception)->toArray());
        return $this->client->SendException(
            $exception,
            $this->metaService->getTags($exception),
            array_merge($customData, $this->metaService->getMeta($exception)),
        );
    }

    public function level(string $type, string|int $level): self
    {
        $this->levels[$type] = $level;

        return $this;
    }

    protected function getExceptionLevel(Throwable $exception): string
    {
        return Arr::first(
            $this->levels,
            fn ($level, $type) => $exception instanceof $type,
            LogLevel::ERROR,
        );
    }
}
