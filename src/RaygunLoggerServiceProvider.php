<?php

namespace LlewellynKevin\RaygunLogger;

use Illuminate\Support\ServiceProvider;

class RaygunLoggerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('raygun-logger.php'),
            ], 'config');

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'raygun-logger');

        // Register the main class to use with the facade
        $this->app->singleton('raygun-logger', function () {
            return new RaygunLogger;
        });
    }
}
