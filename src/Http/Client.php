<?php

namespace LlewellynKevin\RaygunLogger\Http;

use GuzzleHttp\Client as GuzzleClient;
use Raygun4php\Transports\GuzzleAsync;

class Client
{
    public function getClient(): GuzzleAsync
    {
        $uri = config('raygun-logger.base_uri');
        $key = config('raygun-logger.api_key');

        $httpClient = new GuzzleClient([
            'base_uri' => $uri,
            'headers' => [
                'X-ApiKey' => $key,
            ]
        ]);

        return new GuzzleAsync($httpClient);
    }
}
