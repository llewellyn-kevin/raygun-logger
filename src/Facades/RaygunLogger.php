<?php

namespace LlewellynKevin\RaygunLogger\Facades;

use Illuminate\Support\Facades\Facade;
use LlewellynKevin\RaygunLogger\Tests\Mocks\MockRaygunLogger;

/**
 * @method static ?bool handle(Throwable $exception, array $customData = [])
 * @method static self level(string $type, string|int $level)
 *
 * @see \LlewellynKevin\RaygunLogger\RaygunLogger
 */
class RaygunLogger extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'raygun-logger';
    }

    public static function fake()
    {
        static::swap($fake = new MockRaygunLogger(static::getFacadeRoot()));

        return $fake;
    }
}
