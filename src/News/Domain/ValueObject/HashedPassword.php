<?php

namespace App\News\Domain\ValueObject;

use InvalidArgumentException;

/**
 * Holds an already-hashed password. Plain-text passwords must be hashed by the
 * application layer (via Symfony's UserPasswordHasher) BEFORE being wrapped here.
 */
final readonly class HashedPassword
{
    public string $value;

    public function __construct(string $value)
    {
        if ($value === '') {
            throw new InvalidArgumentException('Hashed password must not be blank.');
        }

        $this->value = $value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
