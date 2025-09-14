<?php

declare(strict_types=1);

namespace App\Application\DTO;

final class CreateUserRequest
{
    public function __construct(
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $lastName
    ) {
    }
}
