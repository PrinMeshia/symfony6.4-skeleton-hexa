<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\UserResponse;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use DomainException;

final class GetUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(string $userId): UserResponse
    {
        $user = $this->userRepository->findById(UserId::fromString($userId));

        if (!$user) {
            throw new DomainException(sprintf('User with id %s not found', $userId));
        }

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
