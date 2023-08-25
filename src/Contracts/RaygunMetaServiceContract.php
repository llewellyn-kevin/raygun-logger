<?php

namespace LlewellynKevin\RaygunLogger\Contracts;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use LlewellynKevin\RaygunLogger\DataObjects\RaygunUser;
use Monolog\Logger;
use Throwable;

interface RaygunMetaServiceContract
{
    public function shouldLog(string|int $level, ?Throwable $exception = null): bool;

    public function getTags(Throwable $exception): ?array;

    public function getMeta(Throwable $exception): ?array;

    public function getUser(Throwable $exception): RaygunUser;
}
