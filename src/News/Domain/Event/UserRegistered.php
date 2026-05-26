<?php

namespace App\News\Domain\Event;

use App\News\Domain\ValueObject\Email;
use App\News\Domain\ValueObject\UserID;

final readonly class UserRegistered
{
    public function __construct(
        public UserID $userID,
        public Email $email,
    ) {
    }
}
