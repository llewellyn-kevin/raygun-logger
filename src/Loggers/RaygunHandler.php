<?php

namespace LlewellynKevin\RaygunLogger\Loggers;

use LlewellynKevin\RaygunLogger\Contracts\RaygunLoggerContract as RaygunLogger;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Throwable;

class RaygunHandler extends AbstractProcessingHandler
{
    protected RaygunLogger $raygunLogger;

    /**
     * @param LaraBug $laraBug
     * @param int $level
     * @param bool $bubble
     */
    public function __construct(RaygunLogger $raygunLogger, $level = Logger::ERROR, bool $bubble = true)
    {
        $this->raygunLogger = $raygunLogger;

        parent::__construct($level, $bubble);
    }

    /**
     * @param array $record
     */
    protected function write(LogRecord|array $record): void
    {
        $exception = data_get($record, 'context.exception');
        if ($exception !== null && $exception instanceof Throwable) {
            $this->raygunLogger->handle($exception);

            return;
        }
    }
}
