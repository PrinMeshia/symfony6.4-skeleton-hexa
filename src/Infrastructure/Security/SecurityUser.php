<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\User\Entity\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class SecurityUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private readonly string $id,
        private readonly string $email,
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly string $password = '',
        /** @var string[] */
        private readonly array $roles = ['ROLE_USER']
    ) {
    }

    public static function fromDomainUser(User $user, string $password = ''): self
    {
        return new self(
            id: $user->id()->value(),
            email: $user->email()->value(),
            firstName: $user->firstName(),
            lastName: $user->lastName(),
            password: $password
        );
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // Pas de credentials sensibles à effacer
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Méthodes personnalisées pour l'AuthController
    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }
}
