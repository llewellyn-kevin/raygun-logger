<?php

namespace LlewellynKevin\RaygunLogger\Tests\Commands;

use LlewellynKevin\RaygunLogger\Facades\RaygunLogger;

beforeEach(function () {
    RaygunLogger::fake();
});

it('calls logger handle with default exception', function () {
});

// it('calls logger handle with custom exception', function () {
// });
