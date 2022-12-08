<?php

namespace LlewellynKevin\RaygunLogger\DataObjects;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Model;

class RaygunUser
{
    public function __construct(
        public ?int $id,
        public ?string $firstName,
        public ?string $name,
        public ?string $email,
        public bool $isAnonymous,
    ) {
    }

    public function toArray(): array
    {
        return [
            $this->id,
            $this->firstName,
            $this->name,
            $this->email,
            $this->isAnonymous,
        ];
    }

    public static function fromModel(Authenticatable $user): self
    {
        return new self(
            id: $user->id,
            firstName: explode(' ', $user->name)[0],
            name: $user->name,
            email: $user->email,
            isAnonymous: false,
        );
    }

    public static function anonymous(): self
    {
        return new self(
            id: null,
            firstName: null,
            name: null,
            email: null,
            isAnonymous: true,
        );
    }
}
