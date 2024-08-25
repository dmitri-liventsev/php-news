<?php

namespace App\News\Domain\ValueObject;

use InvalidArgumentException;

class CategoryID
{
    private int $id;

    /**
     * CategoryID constructor.
     *
     * @param int $id
     * @throws InvalidArgumentException if the provided id is not a positive integer
     */
    public function __construct(int $id)
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Category ID must be a positive integer.');
        }

        $this->id = $id;
    }

    /**
     * Get the category ID.
     *
     * @return int
     */
    public function getValue(): int
    {
        return $this->id;
    }

    /**
     * Check if the given category ID is equal to the current one.
     *
     * @param CategoryID $other
     * @return bool
     */
    public function equals(CategoryID $other): bool
    {
        return $this->id === $other->getValue();
    }

    /**
     * Convert the CategoryID to a string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->id;
    }
}
