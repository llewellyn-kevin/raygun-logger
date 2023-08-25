<?php

namespace LlewellynKevin\RaygunLogger\Tests\Mocks;

use Exception;

beforeEach(function () {
    $this->mock = new MockRaygunClient;
});

it('can set and check the most recent user information', function () {
    expect($this->mock->hasUser())->toBeFalse();

    $this->mock->SetUser(5, 'Caleb', 'Caleb Porzio', 'caleb@laravel.com', false);

    expect($this->mock->hasUser())->toBeTrue();
    expect($this->mock->hasUser(['firstName' => 'Caleb']))->toBeTrue();
    expect($this->mock->hasUser(['firstName' => 'Adam']))->toBeFalse();

    $this->mock->SetUser(7, 'Adam', 'Adam Wathan', 'adam@laravel.com', false);

    expect($this->mock->hasUser(['firstName' => 'Caleb']))->toBeFalse();
    expect($this->mock->hasUser(['firstName' => 'Adam']))->toBeTrue();
});


it('can send and check exceptions', function () {
    expect($this->mock->hasLoggedException())->toBeFalse();

    $exceptionOne = new Exception('First exception');
    $exceptionTwo = new Exception('Second exception');
    $exceptionThree = new Exception('Third exception');

    $this->mock->SendException($exceptionOne);
    $this->mock->SendException($exceptionThree);

    expect($this->mock->hasLoggedException($exceptionOne))->toBeTrue();
    expect($this->mock->hasLoggedException($exceptionTwo))->toBeFalse();
    expect($this->mock->hasLoggedException($exceptionThree))->toBeTrue();
});

it('can reset the mock client interface', function () {
    $this->mock->SetUser(5, 'Caleb', 'Caleb Porzio', 'caleb@laravel.com', false);
    $this->mock->SendException(new Exception('First exception'));

    expect($this->mock->hasUser())->toBeTrue();
    expect($this->mock->hasLoggedException())->toBeTrue();

    $this->mock->resetMock();

    expect($this->mock->hasUser())->toBeFalse();
    expect($this->mock->hasLoggedException())->toBeFalse();
});
