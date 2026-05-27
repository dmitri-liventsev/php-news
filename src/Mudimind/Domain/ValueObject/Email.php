<?php

namespace App\Mudimind\Domain\ValueObject;

use InvalidArgumentException;

final readonly class Email
{
    private const MAX_LENGTH = 255;

    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('Email must not be blank.');
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Email must not exceed %d characters.', self::MAX_LENGTH)
            );
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid email address.', $value));
        }

        $this->value = mb_strtolower($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
