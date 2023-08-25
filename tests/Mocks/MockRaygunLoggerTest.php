<?php

namespace LlewellynKevin\RaygunLogger\Tests\Mocks;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use LlewellynKevin\RaygunLogger\Contracts\RaygunLoggerContract;
use Psr\Log\LogLevel;

beforeEach(function () {
    $this->logger = new MockRaygunLogger(
        app('raygun-logger'),
    );
});

it('tracks exceptions passed in', function () {
    $baseException = new Exception('some test');
    $authException = new AuthorizationException('some other test');

    expect($this->logger->hasLoggedException())->toBeFalse();
    expect($this->logger->hasLoggedException($baseException))->toBeFalse();
    expect($this->logger->hasLoggedException($authException))->toBeFalse();

    $this->logger->handle($baseException);

    expect($this->logger->hasLoggedException())->toBeTrue();
    expect($this->logger->hasLoggedException($baseException))->toBeTrue();
    expect($this->logger->hasLoggedException($authException))->toBeFalse();

    $this->logger->handle($authException);

    expect($this->logger->hasLoggedException())->toBeTrue();
    expect($this->logger->hasLoggedException($baseException))->toBeTrue();
    expect($this->logger->hasLoggedException($authException))->toBeTrue();
});

it('tracks levels passed in', function () {
    $baseException = new Exception('some test');
    $authException = new AuthorizationException('some other test');

    expect($this->logger->hasAssignedLevel())->toBeFalse();
    expect($this->logger->hasAssignedLevel($baseException))->toBeFalse();
    expect($this->logger->hasAssignedLevel($authException))->toBeFalse();

    $this->logger->level($baseException, LogLevel::CRITICAL);

    expect($this->logger->hasAssignedLevel())->toBeTrue();
    expect($this->logger->hasAssignedLevel($baseException))->toBeTrue();
    expect($this->logger->hasAssignedLevel($authException))->toBeFalse();

    $this->logger->level($authException, LogLevel::CRITICAL);

    expect($this->logger->hasAssignedLevel())->toBeTrue();
    expect($this->logger->hasAssignedLevel($baseException))->toBeTrue();
    expect($this->logger->hasAssignedLevel($authException))->toBeTrue();
});
