<?php

namespace App\News\Domain\ValueObject;

use InvalidArgumentException;

class ImageID
{
    private int $value;

    /**
     * ImageID constructor.
     *
     * @param int $value
     * @throws InvalidArgumentException
     */
    public function __construct(int $value)
    {
        // Ensure the image ID is a positive integer
        if ($value <= 0) {
            throw new InvalidArgumentException('Invalid image ID: must be a positive integer.');
        }

        $this->value = $value;
    }

    /**
     * Returns the value of the image ID.
     *
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Compares the current ImageID with another one.
     *
     * @param ImageID $other
     * @return bool
     */
    public function equals(ImageID $other): bool
    {
        return $this->value === $other->getValue();
    }

    /**
     * Returns a string representation of the image ID.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }
}