<?php

namespace LlewellynKevin\RaygunLogger\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use LlewellynKevin\RaygunLogger\Contracts\RaygunMetaService;
use LlewellynKevin\RaygunLogger\DataObjects\RaygunUser;
use Monolog\Logger;
use Throwable;

class MetaService implements RaygunMetaService
{
    public function shouldLog(string|int $level, ?Throwable $exception = null): bool
    {
        if (is_null($exception)) {
            return $this->hasLoggableLevel($level);
        }

        return $this->hasLoggableLevel($level) && !$this->throwableOnBlacklist($exception);
    }

    public function getTags(Throwable $exception): ?array
    {
        // String array of tags
        return [
            App::environment(),
        ];
    }

    public function getMeta(Throwable $exception): ?array
    {
        // Key-value pairs
        return [];
    }

    public function getUser(Throwable $exception): RaygunUser
    {
        $user = Auth::user();

        if (is_null($user)) {
            return RaygunUser::anonymous();
        }

        return RaygunUser::fromModel($user);
    }

    protected function hasLoggableLevel(int|string $level): bool
    {
        $exceptionLevel = Logger::toMonologLevel($level);
        $baseLevel = Logger::toMonologLevel(config('raygun-logger.level'));
        return $exceptionLevel >= $baseLevel;
    }

    protected function throwableOnBlacklist(Throwable $e): bool
    {
        $blacklist = config('raygun-logger.blacklist', []);

        foreach ($blacklist as $exception) {
            if (get_class($e) === $exception) return true;
        }

        return false;
    }
}
