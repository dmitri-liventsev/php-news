<?php

namespace App\News\Domain\ValueObject;

use InvalidArgumentException;

class ArticleID
{
    private int $value;

    /**
     * ArticleID constructor.
     *
     * @param int $value
     * @throws InvalidArgumentException
     */
    public function __construct(int $value)
    {
        // Ensure the article ID is a positive integer
        if ($value <= 0) {
            throw new InvalidArgumentException('Invalid article ID: must be a positive integer.');
        }

        $this->value = $value;
    }

    /**
     * Returns the value of the article ID.
     *
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Compares the current ArticleID with another one.
     *
     * @param ArticleID $other
     * @return bool
     */
    public function equals(ArticleID $other): bool
    {
        return $this->value === $other->getValue();
    }

    /**
     * Returns a string representation of the article ID.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }
}
