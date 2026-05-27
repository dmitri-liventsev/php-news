<?php

namespace App\Mudimind\Domain\ValueObject;

use InvalidArgumentException;

final readonly class MasseurID
{
    public int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Masseur ID must be a positive integer.');
        }

        $this->value = $value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
