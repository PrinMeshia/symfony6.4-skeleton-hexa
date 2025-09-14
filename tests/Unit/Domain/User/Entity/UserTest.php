<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\User\Entity;

use App\Domain\User\Entity\User;
use App\Domain\User\Event\UserCreatedEvent;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testUserCreation(): void
    {
        $id = new UserId();
        $email = new Email('test@example.com');
        $firstName = 'John';
        $lastName = 'Doe';

        $user = new User($id, $email, $firstName, $lastName);

        $this->assertEquals($id, $user->id());
        $this->assertEquals($email, $user->email());
        $this->assertEquals($firstName, $user->firstName());
        $this->assertEquals($lastName, $user->lastName());
        $this->assertEquals('John Doe', $user->fullName());
    }

    public function testUserEmitsCreatedEvent(): void
    {
        $id = new UserId();
        $email = new Email('test@example.com');
        $firstName = 'John';
        $lastName = 'Doe';

        $user = new User($id, $email, $firstName, $lastName);

        $events = $user->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserCreatedEvent::class, $events[0]);
    }

    public function testUpdateEmail(): void
    {
        $user = new User(
            new UserId(),
            new Email('old@example.com'),
            'John',
            'Doe'
        );

        $newEmail = new Email('new@example.com');
        $user->updateEmail($newEmail);

        $this->assertEquals($newEmail, $user->email());
    }

    public function testUpdateName(): void
    {
        $user = new User(
            new UserId(),
            new Email('test@example.com'),
            'John',
            'Doe'
        );

        $user->updateName('Jane', 'Smith');

        $this->assertEquals('Jane', $user->firstName());
        $this->assertEquals('Smith', $user->lastName());
        $this->assertEquals('Jane Smith', $user->fullName());
    }
}
