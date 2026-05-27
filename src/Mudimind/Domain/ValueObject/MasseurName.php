<?php

namespace App\Mudimind\Domain\ValueObject;

use InvalidArgumentException;

final readonly class MasseurName
{
    private const MAX_LENGTH = 255;

    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('Masseur name must not be blank.');
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Masseur name must not exceed %d characters.', self::MAX_LENGTH)
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
