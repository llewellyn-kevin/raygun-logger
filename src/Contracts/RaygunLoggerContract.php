<?php

namespace LlewellynKevin\RaygunLogger\Contracts;

use Throwable;

interface RaygunLoggerContract
{
    public function handle(Throwable $exception, array $customData = []);

    public function level(string $type, string|int $level): self;
}
