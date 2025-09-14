<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class UserDoctrineEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 100)]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 100)]
    private string $lastName;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    public function __construct()
    {
        // Doctrine constructor
    }

    public static function fromDomain(User $user): self
    {
        $entity = new self();
        $entity->id = $user->id()->value();
        $entity->email = $user->email()->value();
        $entity->firstName = $user->firstName();
        $entity->lastName = $user->lastName();
        $entity->createdAt = $user->createdAt();
        $entity->updatedAt = $user->updatedAt();

        return $entity;
    }

    public function toDomain(): User
    {
        return new User(
            UserId::fromString($this->id),
            new Email($this->email),
            $this->firstName,
            $this->lastName
        );
    }

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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateFromDomain(User $user): void
    {
        $this->email = $user->email()->value();
        $this->firstName = $user->firstName();
        $this->lastName = $user->lastName();
        $this->updatedAt = $user->updatedAt();
    }
}
