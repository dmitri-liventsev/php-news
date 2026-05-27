<?php

namespace App\Mudimind\Domain\Repository;

use App\Mudimind\Domain\Entity\Book;
use App\Mudimind\Domain\ValueObject\BookID;
use App\Mudimind\Domain\ValueObject\MasseurID;
use DateTimeInterface;

interface BookRepositoryInterface
{
    public function save(Book $book): BookID;

    /**
     * All (non-deleted) bookings for a masseur on the calendar day of $day,
     * used to check availability before creating a new booking.
     *
     * @return Book[]
     */
    public function findByMasseurAndDay(MasseurID $masseurId, DateTimeInterface $day): array;
}
