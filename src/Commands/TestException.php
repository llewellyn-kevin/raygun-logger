<?php

namespace LlewellynKevin\RaygunLogger\Commands;

use Exception;
use Illuminate\Console\Command;
use LlewellynKevin\RaygunLogger\RaygunLoggerFacade;

class TestException extends Command
{
    protected $signature = 'raygun:test {exception?}';

    protected $description = 'Generate a test exception and send it to Raygun';

    public function handle()
    {
        try {
            if (is_null(config('raygun-logger.base_uri'))) {
                $this->error('✗ RAYGUN_BASE_URI environment variable not found. Please add to your .env');
            } else {
                $this->info('✓ Found Raygun URL');
            }

            if (is_null(config('raygun-logger.api_key'))) {
                $this->error('✗ RAYGUN_API_KEY environment variable not found. Please add to your .env');
            } else {
                $this->info('✓ Found Raygun key');
            }

            $response = RaygunLoggerFacade::handle(
                $this->generateException()
            );

            dd($response);
        } catch (\Exception $ex) {
            $this->error("✗ {$ex->getMessage()}");
        }
    }

    public function generateException(): ?Exception
    {
        try {
            throw new Exception($this->argument('exception') ?? 'This is a test exception from the LaraBug console');
        } catch (Exception $ex) {
            return $ex;
        }
    }
}
