<?php

namespace LlewellynKevin\RaygunLogger\Loggers;

use LlewellynKevin\RaygunLogger\RaygunLogger;
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
    protected function write(LogRecord $record): void
    {
        if (isset($record->context['exception']) && $record->context['exception'] instanceof Throwable) {
            $this->raygunLogger->handle(
                $record->context['exception']
            );

            return;
        }
    }
}