<?php

namespace LlewellynKevin\RaygunLogger\Http;

use GuzzleHttp\Client as GuzzleClient;
use Raygun4php\Transports\GuzzleAsync;

class Client
{
    public function getClient(): GuzzleAsync
    {
        $uri = config('raygun-logger.base_uri');
        if (is_null($uri)) {
            throw new \Exception('No base url set up for Raygun. Add `RAYGUN_BASE_URI` in your .env');
        }

        $key = config('raygun-logger.api_key');
        if (is_null($key)) {
            throw new \Exception('No api key set up for Raygun. Add `RAYGUN_API_KEY` in your .env');
        }

        $httpClient = new GuzzleClient([
            'base_uri' => $uri,
            'headers' => [
                'X-ApiKey' => $key,
            ]
        ]);

        return new GuzzleAsync($httpClient);
    }
}
