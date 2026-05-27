<?php

namespace App\Mudimind\Domain\Event;

use App\Mudimind\Domain\ValueObject\BookID;
use App\Mudimind\Domain\ValueObject\MasseurID;
use App\Shared\Domain\Event\AbstractDomainEvent;
use DateTimeImmutable;

final readonly class BookTimeCreated extends AbstractDomainEvent
{
    public function __construct(
        public BookID $bookId,
        public MasseurID $masseurId,
        public DateTimeImmutable $startAt,
        public DateTimeImmutable $endAt,
    ) {
        parent::__construct();
    }
}
