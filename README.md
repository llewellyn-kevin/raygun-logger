# Laravel package for logging errors to Raygun

[![Latest Version on Packagist](https://img.shields.io/packagist/v/llewellyn-kevin/raygun-logger.svg?style=flat-square)](https://packagist.org/packages/llewellyn-kevin/raygun-logger)
[![Total Downloads](https://img.shields.io/packagist/dt/llewellyn-kevin/raygun-logger.svg?style=flat-square)](https://packagist.org/packages/llewellyn-kevin/raygun-logger)
![GitHub Actions](https://github.com/llewellyn-kevin/raygun-logger/actions/workflows/main.yml/badge.svg)

This package serves as a wrapper around [Raygun4php](https://github.com/MindscapeHQ/raygun4php) to make it quick and easy to get your Laravel project logging to [Raygun](https://raygun.com/).

> **Warning**
> This package is being developed alongside and being used with production applications. But it is not tagged for with version 1.0 because we can only validate those particular use cases. We will want to catch any edge cases and finalize every base use case before encouraging users to use this in critical projects.

## Installation

You can install the package via composer:

```bash
composer require llewellyn-kevin/raygun-logger
```

In your application's `config/logging.php`, add the raygun channel config and add it to the stack config:

```php
[
    'channel' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'raygun'],
            // ...
        ],

        // ...

        'raygun' => [
            'driver' => 'raygun',
        ],
    ],
]
```

Update your `.env` with the Raygun url and key you want to use:

```
RAYGUN_URI=https://api.raygun.com
RAYGUN_KEY=[[project key here]]
```

You can test your install with a console command:

```bash
php artisan ragun:test
```

Or with a custom error message:

```bash
php artisan ragun:test "The fox already jumped over the dog."
```

This will check for environment variables, report on any missing, and send an exception with default or custom text to Raygun so you can check to make sure it shows up.

## Usage

Errors should be automatically sent to raygun via the stack. But if you need to manually send an exception to Raygun for any reason you can do that with the `RaygunLogger` facade:

```php
RaygunLogger::handle(new Exception('The fox already jumped over the dog.'));
```

You can add metadata to these requests in an associative array as well:

```php
RaygunLogger::handle(new Exception('The fox already jumped over the dog.'), [
    'foxname' => 'Tod',
    'dogname' => 'Copper',
]);
```

> **Note**
> This is the main method of the entire package. So it enforces all core logic. This means Exception types that have been blacklisted or do not meet the level requirement will still be suppressed and NOT be sent to Raygun.

### Filtering

You may not want every error to show up in Raygun. There are two ways to suppress an error:

1. Blacklist the exception type

In the `raygun-logger` config, you will find a parameter called blacklist. Any exceptions added to this array will not be reported to Raygun. To append to this list, you will first have to publish the config:

```bash
php artisan vendor:publish --tag=config
```

2. Setting error levels

You may wish to specify the log levels for various exceptions in your application. You can register these levels in the `RaygunLogger` facade:

```php
RaygunLogger::level(Exception::class, LogLevel::CRITICAL);
```

Then you may update the `raygun-logger` config with the base level you wish to report on. Only exceptions with the specified level or higher will be sent to Raygun.

## Testing

This package uses [Pest](https://pestphp.com/) for it's tests. For simplicity, there is a composer script to run the whole suite:

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email llewellynkevin1@gmail.com instead of using the issue tracker.

## Credits

- [All Contributors](../../contributors)
- This package is massively inspired by the work done over at [LaraBug](https://www.larabug.com/). The goal was to have a LaraBug like experience for setting up Raygun. They have an amazing product and much of their code is used as the foundation for this package. If you don't need Raygun, you may give that a shot as a free alternative.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
