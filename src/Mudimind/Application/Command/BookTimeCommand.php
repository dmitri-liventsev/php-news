<?php

namespace App\Mudimind\Application\Command;

use App\Mudimind\Domain\ValueObject\Email;
use App\Mudimind\Domain\ValueObject\MasseurID;
use DateTime;

readonly class BookTimeCommand
{
    public function __construct(
        public DateTime $startedAt,
        public DateTime $endedAt,
        public MasseurID $masseurID,
        public Email $email,
    ) {
    }
}
