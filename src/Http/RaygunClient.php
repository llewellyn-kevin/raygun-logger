<?php

namespace LlewellynKevin\RaygunLogger\Http;

use LlewellynKevin\RaygunLogger\Contracts\RaygunClientContract;
use Raygun4php\RaygunClient as Raygun4phpRaygunClient;

class RaygunClient extends Raygun4phpRaygunClient implements RaygunClientContract
{
}
