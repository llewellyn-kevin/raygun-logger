<?php

namespace LlewellynKevin\RaygunLogger;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use LlewellynKevin\RaygunLogger\Contracts\RaygunMetaService;
use Psr\Log\LogLevel;
use Raygun4php\RaygunClient;
use Throwable;

class RaygunLogger
{
    protected array $levels = [];

    public function __construct(
        public RaygunMetaService $metaService,
        public RaygunClient $client,
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
