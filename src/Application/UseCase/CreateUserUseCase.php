<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\CreateUserRequest;
use App\Application\DTO\UserResponse;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use DomainException;

final class CreateUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(CreateUserRequest $request): UserResponse
    {
        $email = new Email($request->email);

        // Vérifier que l'email n'existe pas déjà
        if ($this->userRepository->existsByEmail($email)) {
            throw new DomainException(sprintf('User with email %s already exists', $email->value()));
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
