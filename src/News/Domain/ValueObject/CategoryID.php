<?php

namespace App\News\Domain\ValueObject;

use InvalidArgumentException;

final readonly class CategoryID
{
    public int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Category ID must be a positive integer.');
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