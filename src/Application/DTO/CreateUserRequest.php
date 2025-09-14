<?php

declare(strict_types=1);

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Email is required')]
        #[Assert\Email(message: 'Invalid email format')]
        #[Assert\Length(max: 255, maxMessage: 'Email cannot be longer than {{ limit }} characters')]
        public readonly string $email,

        #[Assert\NotBlank(message: 'First name is required')]
        #[Assert\Length(min: 2, max: 100, minMessage: 'First name must be at least {{ limit }} characters', maxMessage: 'First name cannot be longer than {{ limit }} characters')]
        #[Assert\Regex(pattern: '/^[a-zA-ZÀ-ÿ\s\-\']+$/', message: 'First name contains invalid characters')]
        public readonly string $firstName,

        #[Assert\NotBlank(message: 'Last name is required')]
        #[Assert\Length(min: 2, max: 100, minMessage: 'Last name must be at least {{ limit }} characters', maxMessage: 'Last name cannot be longer than {{ limit }} characters')]
        #[Assert\Regex(pattern: '/^[a-zA-ZÀ-ÿ\s\-\']+$/', message: 'Last name contains invalid characters')]
        public readonly string $lastName
    ) {
    }
}
