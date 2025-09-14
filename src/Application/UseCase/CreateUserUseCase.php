<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\CreateUserRequest;
use App\Application\DTO\UserResponse;
use App\Domain\User\Entity\User;
use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Event\DomainEventDispatcher;

final class CreateUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly DomainEventDispatcher $eventDispatcher
    ) {
    }

    public function execute(CreateUserRequest $request): UserResponse
    {
        $email = new Email($request->email);

        // Vérifier que l'email n'existe pas déjà
        if ($this->userRepository->existsByEmail($email)) {
            throw UserAlreadyExistsException::withEmail($email->value());
        }

        // Créer l'utilisateur
        $user = new User(
            new UserId(),
            $email,
            $request->firstName,
            $request->lastName
        );

        // Sauvegarder
        $this->userRepository->save($user);

        // Dispatcher les événements du domaine
        $this->eventDispatcher->dispatch($user->pullDomainEvents());

        // Retourner la réponse
        return new UserResponse(
            $user->id()->value(),
            $user->email()->value(),
            $user->firstName(),
            $user->lastName(),
            $user->fullName(),
            $user->createdAt()->format('Y-m-d H:i:s'),
            $user->updatedAt()->format('Y-m-d H:i:s')
        );
    }
}
