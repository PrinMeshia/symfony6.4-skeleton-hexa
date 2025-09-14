<?php

declare(strict_types=1);

namespace App\Infrastructure\Event\Listener;

use App\Domain\User\Event\UserCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'user.created')]
final class UserCreatedEventListener
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(UserCreatedEvent $event): void
    {
        $this->logger->info('User created event received', [
            'user_id' => $event->userId()->value(),
            'email' => $event->email()->value(),
            'first_name' => $event->firstName(),
            'last_name' => $event->lastName(),
        ]);

        // Ici vous pouvez ajouter d'autres actions :
        // - Envoyer un email de bienvenue
        // - Créer un profil par défaut
        // - Notifier d'autres services
        // - etc.
    }
}
