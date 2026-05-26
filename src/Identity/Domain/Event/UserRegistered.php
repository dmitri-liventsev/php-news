<?php

namespace App\Identity\Domain\Event;

use App\Identity\Domain\ValueObject\Email;
use App\Identity\Domain\ValueObject\UserID;
use App\Shared\Domain\Event\AbstractDomainEvent;

final readonly class UserRegistered extends AbstractDomainEvent
{
    public function __construct(
        public UserID $userID,
        public Email $email,
    ) {
        parent::__construct();
    }
}
