<?php

declare(strict_types=1);

namespace App\Domain\Event;

use DateTimeImmutable;

abstract class DomainEvent
{
    private DateTimeImmutable $occurredOn;
    private string $eventId;

    public function __construct()
    {
        $this->occurredOn = new DateTimeImmutable();
        $this->eventId = uniqid('event_', true);
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    abstract public function eventName(): string;
}
