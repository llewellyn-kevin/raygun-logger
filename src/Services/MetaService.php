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
        $exceptionLevel = Logger::toMonologLevel($level);
        $baseLevel = Logger::toMonologLevel(config('raygun-logger.level'));
        return $exceptionLevel >= $baseLevel;
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
}
