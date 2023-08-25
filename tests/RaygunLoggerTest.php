<?php

namespace LlewellynKevin\RaygunLogger\Tests;

use Illuminate\Auth\Access\AuthorizationException;
use LlewellynKevin\RaygunLogger\Contracts\RaygunMetaServiceContract;
use LlewellynKevin\RaygunLogger\RaygunLogger;
use LlewellynKevin\RaygunLogger\Tests\Mocks\MockRaygunClient;
use Psr\Log\LogLevel;

beforeEach(function () {
    $this->metaService = app(RaygunMetaServiceContract::class);
    $this->client = new MockRaygunClient;
    $this->logger = new RaygunLogger(
        $this->metaService,
        $this->client,
    );

    config([
        'app.env' => 'testing',
        'raygun-logger.environments' => ['testing'],
        'raygun-logger.level' => LogLevel::ERROR,
    ]);
});

it('assigns logging levels to exceptions', function () {
    expect(invade($this->logger)->levels)->toBe([]);

    $this->logger->level(AuthorizationException::class, LogLevel::WARNING);

    expect(invade($this->logger)->levels)->toBe([
        AuthorizationException::class => LogLevel::WARNING,
    ]);
});

it('gets the level for a registered exception type', function () {
    $exception = new AuthorizationException('Charles Boyle lost access.');

    expect(invade($this->logger)->getExceptionLevel($exception))->toBe(LogLevel::ERROR);

    $this->logger->level(AuthorizationException::class, LogLevel::WARNING);

    expect(invade($this->logger)->getExceptionLevel($exception))->toBe(LogLevel::WARNING);
});

it('logs exceptions', function () {
    $exception = new AuthorizationException('Charles Boyle lost access.');

    expect($this->client->hasLoggedException($exception))->toBeFalse();

    $this->logger->handle($exception);

    expect($this->client->hasLoggedException($exception))->toBeTrue();
});

it('does not log when outside registered environments', function () {
    config([
        'app.env' => 'testing',
        'raygun-logger.environments' => ['staging'],
    ]);

    $exception = new AuthorizationException('Charles Boyle lost access.');

    expect($this->logger->handle($exception))->toBeFalse();
    expect($this->client->hasLoggedException($exception))->toBeFalse();

    config(['raygun-logger.environments' => ['testing']]);

    $this->logger->handle($exception);
    expect($this->client->hasLoggedException($exception))->toBeTrue();
});

it('does not log when the exception level is too low', function () {
    $exception = new AuthorizationException('Charles Boyle lost access.');

    $this->logger->handle($exception);
    expect($this->client->hasLoggedException($exception))->toBeTrue();
    $this->client->resetMock();

    $this->logger->level(AuthorizationException::class, LogLevel::WARNING);

    $this->logger->handle($exception);
    expect($this->client->hasLoggedException($exception))->toBeFalse();
});
