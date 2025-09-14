<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\Event\DomainEvent;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;

final class UserCreatedEvent extends DomainEvent
{
    public function __construct(
        private readonly UserId $userId,
        private readonly Email $email,
        private readonly string $firstName,
        private readonly string $lastName
    ) {
        parent::__construct();
    }

    public function eventName(): string
    {
        return 'user.created';
    }

    public function userId(): UserId
    {
        return $this->userId;
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
}
