<?php

namespace LlewellynKevin\RaygunLogger\Tests\Mocks;

use Illuminate\Support\Collection;
use LlewellynKevin\RaygunLogger\Contracts\RaygunClientContract;
use LlewellynKevin\RaygunLogger\DataObjects\RaygunUser;
use Throwable;

class MockRaygunClient implements RaygunClientContract
{
    public ?RaygunUser $currentUser = null;

    public Collection $thrownExceptions;

    public function __construct()
    {
        $this->thrownExceptions = collect([]);
    }

    public function SetUser($user = null, $firstName = null, $fullName = null, $email = null, $isAnonymous = null, $uuid = null)
    {
        $this->currentUser = new RaygunUser($user, $firstName, $fullName, $email, $isAnonymous);
    }

    public function SendException($throwable, $tags = null, $userCustomData = null, $timestamp = null)
    {
        $this->thrownExceptions->push($throwable);
    }

    public function resetMock()
    {
        $this->currentUser = null;
        $this->thrownExceptions = collect([]);
    }

    public function hasUser(?array $properties = null): bool
    {
        if (is_null($this->currentUser)) {
            return false;
        } else if (empty($properties)) {
            return true;
        }

        foreach ($properties as $key => $value) {
            if ($this->currentUser->$key !== $value) {
                return false;
            }
        }

        return true;
    }

    public function hasLoggedException(?Throwable $e = null)
    {
        if (is_null($e)) return !$this->thrownExceptions->isEmpty();

        return $this->thrownExceptions->search($e) !== false;
    }
}
