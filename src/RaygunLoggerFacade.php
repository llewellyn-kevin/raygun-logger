<?php

namespace LlewellynKevin\RaygunLogger;

use Illuminate\Support\Facades\Facade;

/**
 * @see \LlewellynKevin\RaygunLogger\Skeleton\SkeletonClass
 */
class RaygunLoggerFacade extends Facade
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
}
