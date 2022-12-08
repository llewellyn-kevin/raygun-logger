<?php

namespace LlewellynKevin\RaygunLogger;

use Illuminate\Support\ServiceProvider;
use LlewellynKevin\RaygunLogger\Contracts\RaygunMetaService;
use LlewellynKevin\RaygunLogger\Http\Client;
use LlewellynKevin\RaygunLogger\Loggers\RaygunHandler;
use LlewellynKevin\RaygunLogger\Services\MetaService;
use Monolog\Logger;
use Raygun4php\RaygunClient;
use Raygun4php\Transports\GuzzleAsync;

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

        // Any requests to Raygun server will be send right before shutdown.
        register_shutdown_function([
            $this->app->make(GuzzleAsync::class), 'wait'
        ]);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'raygun-logger');

        // Bind public interfaces
        $this->app->bind(RaygunMetaService::class, MetaService::class);

        // Register the async transport.
        $this->app->singleton(GuzzleAsync::class, function ($app) {
            return (new Client)->getClient();
        });

        // Register the RaygunClient instance.
        $this->app->singleton(RaygunClient::class, function ($app) {
            return new RaygunClient($app->make(GuzzleAsync::class));
        });

        // Register the main class to use with the facade
        $this->app->singleton('raygun-logger', function () {
            return new RaygunLogger(
                $this->app->get(RaygunMetaService::class),
                $this->app->get(RaygunClient::class),
            );
        });

        // Extend the application logger so raygun can be added
        if ($this->app['log'] instanceof \Illuminate\Log\LogManager) {
            $this->app['log']->extend('raygun', function ($app, $config) {
                $handler = new RaygunHandler($app['raygun-logger']);
                $logger = new Logger('raygun', [$handler]);
                return $logger;
            });
        }
    }
}
