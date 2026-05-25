<?php

namespace App\News\Domain\ValueObject;

use InvalidArgumentException;

final readonly class ShortDescription
{
    private const MAX_LENGTH = 500;

    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('Short description must not be blank.');
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Short description must not exceed %d characters.', self::MAX_LENGTH)
            );
        }

        $this->value = $value;
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