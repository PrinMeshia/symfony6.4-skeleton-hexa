<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\Event\DomainEvent;
use App\Domain\User\Event\UserCreatedEvent;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;

class User
{
    private UserId $id;
    private Email $email;
    private string $firstName;
    private string $lastName;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    /** @var DomainEvent[] */
    private array $domainEvents = [];

    public function __construct(
        UserId $id,
        Email $email,
        string $firstName,
        string $lastName
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();

        // Émettre l'événement de création
        $this->recordDomainEvent(new UserCreatedEvent($id, $email, $firstName, $lastName));
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function fullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateEmail(Email $email): void
    {
        $this->email = $email;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateName(string $firstName, string $lastName): void
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function recordDomainEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * @return DomainEvent[]
     */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
