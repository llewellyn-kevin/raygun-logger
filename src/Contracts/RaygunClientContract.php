<?php

namespace LlewellynKevin\RaygunLogger\Contracts;

/**
 * We use this contract for mocking the raygun client from the Raygun4php package.
 * Since we only use it for binding and mocking, we only require methods this packge
 * actually uses--this should prevent it from getting out of sync as often.
 */
interface RaygunClientContract
{
    public function SetUser($user = null, $firstName = null, $fullName = null, $email = null, $isAnonymous = null, $uuid = null);

    public function SendException($throwable, $tags = null, $userCustomData = null, $timestamp = null);
}
