<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use Symfony\Component\Uid\Uuid;

final class UserId
{
    private string $value;

    public function __construct(?string $id = null)
    {
        $this->value = $id ?? (string) Uuid::v4();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(UserId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }
}
