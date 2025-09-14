<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\UseCase;

use App\Application\DTO\CreateUserRequest;
use App\Application\UseCase\CreateUserUseCase;
use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Event\DomainEventDispatcher;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class CreateUserUseCaseTest extends TestCase
{
    private UserRepositoryInterface&MockObject $userRepository;
    private DomainEventDispatcher&MockObject $eventDispatcher;
    private CreateUserUseCase $useCase;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(DomainEventDispatcher::class);
        $this->useCase = new CreateUserUseCase($this->userRepository, $this->eventDispatcher);
    }

    public function testCreateUserSuccessfully(): void
    {
        $request = new CreateUserRequest('test@example.com', 'John', 'Doe');

        $this->userRepository
            ->expects($this->once())
            ->method('existsByEmail')
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch');

        $response = $this->useCase->execute($request);

        $this->assertEquals('test@example.com', $response->email);
        $this->assertEquals('John', $response->firstName);
        $this->assertEquals('Doe', $response->lastName);
        $this->assertEquals('John Doe', $response->fullName);
    }

    public function testCreateUserThrowsExceptionWhenEmailExists(): void
    {
        $request = new CreateUserRequest('existing@example.com', 'John', 'Doe');

        $this->userRepository
            ->expects($this->once())
            ->method('existsByEmail')
            ->willReturn(true);

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        $this->expectException(UserAlreadyExistsException::class);

        $this->useCase->execute($request);
    }
}
