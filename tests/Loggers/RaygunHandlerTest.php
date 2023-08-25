<?php

use LlewellynKevin\RaygunLogger\Facades\RaygunLogger;
use LlewellynKevin\RaygunLogger\Loggers\RaygunHandler;
use Monolog\LogRecord;
use Psr\Log\LogLevel;

beforeEach(function() {
    $this->raygunLogger = RaygunLogger::fake();
    $this->raygunHandler = new PartialMockHandler($this->raygunLogger);
});

it('does nothing with empty log record', function() {
    $log = new MockLogRecord;
    $this->raygunHandler->handle($log->toArray());
    expect($this->raygunLogger->hasLoggedException())
        ->toBe(false);
});

it('does something with actual log record', function() {
    $log = MockLogRecord::withError();
    $this->raygunHandler->handle($log->toArray());
    expect($this->raygunLogger->hasLoggedException())
        ->toBe(true);
});

it('works with LogRecord objects', function() {
    $this->raygunHandler->classLogRecordOverride = MockLogRecord::withError();
    $this->raygunHandler->handle([]);
    expect($this->raygunLogger->hasLoggedException())
        ->toBe(true);
});

class PartialMockHandler extends RaygunHandler
{
    public ?LogRecord $classLogRecordOverride = null;

    public function handle(array $record): bool
    {
        $this->write($this->classLogRecordOverride ?? $record);
        return true;
    }
}

class MockLogRecord implements LogRecord
{
    public ?array $context = [];

    public function toArray(): array
    {
        return [
            'context' => $this->context,
            'level' => LogLevel::DEBUG,
            'extra' => [],
        ];
    }

    public static function withError()
    {
        $static = new static;
        $static->context = [
            'exception' => new Exception('test exception'),
        ];
        return $static;
    }

    public function offsetExists(mixed $offset): bool
    {
        return !is_null($this->offsetGet($offset));
    }

    public function offsetGet(mixed $offset): mixed
    {
        return data_get($this->toArray(), $offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        data_set($this, $offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->$offset = null;
    }
}
