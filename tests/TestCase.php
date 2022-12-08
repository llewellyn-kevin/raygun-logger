<?php

namespace LlewellynKevin\RaygunLogger\Tests;

use LlewellynKevin\RaygunLogger\RaygunLoggerServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getPackageProviders($app): array
    {
        return [RaygunLoggerServiceProvider::class];
    }
}
