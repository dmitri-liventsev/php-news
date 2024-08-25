<?php

namespace App\News\Domain\ValueObject;

use InvalidArgumentException;

class CommentID
{
    private int $id;

    /**
     * CommentID constructor.
     *
     * @param int $id
     * @throws InvalidArgumentException if the provided id is not a positive integer
     */
    public function __construct(int $id)
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Comment ID must be a positive integer.');
        }

        $this->id = $id;
    }

    /**
     * Get the comment ID.
     *
     * @return int
     */
    public function getValue(): int
    {
        return $this->id;
    }

    /**
     * Check if the given comment ID is equal to the current one.
     *
     * @param CommentID $other
     * @return bool
     */
    public function equals(CommentID $other): bool
    {
        return $this->id === $other->getValue();
    }

    /**
     * Convert the CommentID to a string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->id;
    }
}
