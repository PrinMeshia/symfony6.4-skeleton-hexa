<?php

declare(strict_types=1);

namespace App\Infrastructure\Event;

use App\Domain\Event\DomainEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DomainEventDispatcher
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param DomainEvent[] $events
     */
    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            $this->logger->info('Dispatching domain event', [
                'event_name' => $event->eventName(),
                'event_id' => $event->eventId(),
                'occurred_on' => $event->occurredOn()->format('Y-m-d H:i:s'),
            ]);

            $this->eventDispatcher->dispatch($event, $event->eventName());
        }
    }
}
