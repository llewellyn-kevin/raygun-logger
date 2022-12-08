<?php

namespace LlewellynKevin\RaygunLogger\Tests\Services;

use Exception;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use LlewellynKevin\RaygunLogger\DataObjects\RaygunUser;
use LlewellynKevin\RaygunLogger\Services\MetaService;
use Psr\Log\LogLevel;

beforeEach(function () {
    $this->raygunService = new MetaService();
    $this->exception = new Exception('Fake exception');
});

dataset('logLevels', [
    'Debug' => [LogLevel::DEBUG, [LogLevel::DEBUG, LogLevel::INFO, LogLevel::NOTICE, LogLevel::WARNING, LogLevel::ERROR, LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::EMERGENCY], []],
    'Info' => [LogLevel::INFO, [LogLevel::INFO, LogLevel::NOTICE, LogLevel::WARNING, LogLevel::ERROR, LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::EMERGENCY], [LogLevel::DEBUG]],
    'Notice' => [LogLevel::NOTICE, [LogLevel::NOTICE, LogLevel::WARNING, LogLevel::ERROR, LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::EMERGENCY], [LogLevel::DEBUG, LogLevel::INFO]],
    'Warning' => [LogLevel::WARNING, [LogLevel::WARNING, LogLevel::ERROR, LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::EMERGENCY], [LogLevel::DEBUG, LogLevel::INFO, LogLevel::NOTICE]],
    'Error' => [LogLevel::ERROR, [LogLevel::ERROR, LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::EMERGENCY], [LogLevel::DEBUG, LogLevel::INFO, LogLevel::NOTICE, LogLevel::WARNING]],
    'Critical' => [LogLevel::CRITICAL, [LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::EMERGENCY], [LogLevel::DEBUG, LogLevel::INFO, LogLevel::NOTICE, LogLevel::WARNING, LogLevel::ERROR]],
    'Alert' => [LogLevel::ALERT, [LogLevel::ALERT, LogLevel::EMERGENCY], [LogLevel::DEBUG, LogLevel::INFO, LogLevel::NOTICE, LogLevel::WARNING, LogLevel::ERROR, LogLevel::CRITICAL]],
    'Emergency' => [LogLevel::EMERGENCY, [LogLevel::EMERGENCY], [LogLevel::DEBUG, LogLevel::INFO, LogLevel::NOTICE, LogLevel::WARNING, LogLevel::ERROR, LogLevel::CRITICAL, LogLevel::ALERT]],
]);

it('only_logs_at_appropriate_levels', function (string $configLevel, array $shouldLog, array $shouldNotLog) {
    config()->set('raygun-logger.level', $configLevel);
    config()->set('raygun-logger.blacklist', []);
    collect($shouldLog)->each(fn ($level) => expect($this->raygunService->shouldLog($level))->toBeTrue());
    collect($shouldNotLog)->each(fn ($level) => expect($this->raygunService->shouldLog($level))->toBeFalse());
})->with('logLevels');

it('only_logs_exceptions_not_on_blacklist', function () {
    $blacklist = [
        \Exception::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
    ];
    $whitelist = [
        \Illuminate\Auth\AuthenticationException::class,
    ];

    config()->set('raygun-logger.level', 'DEBUG');
    config()->set('raygun-logger.blacklist', $blacklist);

    collect($whitelist)->each(
        fn (string $exception) => expect($this->raygunService->shouldLog('ERROR', new $exception('test')))
            ->toBeTrue()
    );
    collect($blacklist)->each(
        fn (string $exception) => expect($this->raygunService->shouldLog('ERROR', new $exception('test')))
            ->toBeFalse()
    );
});

it('gets_current_tags_for_an_exception', function () {
    App::shouldReceive('environment')->andReturn('test');

    expect($this->raygunService->getTags($this->exception))->toBe(['test']);
});

it('returns_null_for_the_current_user_if_none_authorized', function () {
    expect($this->raygunService->getUser($this->exception)->toArray())
        ->toBe(RaygunUser::anonymous()->toArray());
});

it('returns_details_about_the_current_user', function () {
    $user = User::make();
    $user->name = 'Taylor Otwell';
    $user->email =  'totwell@laravel.com';
    $user->email_verified_at = date("Y-m-d H:i:s");
    $user->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // password
    $user->remember_token = Str::random(10);
    $user->id = 1;
    Auth::shouldReceive('user')->andReturn($user);

    $raygunUser = new RaygunUser(1, 'Taylor', 'Taylor Otwell', 'totwell@laravel.com', false,);

    expect($this->raygunService->getUser($this->exception)->toArray())
        ->toBe($raygunUser->toArray());
});
