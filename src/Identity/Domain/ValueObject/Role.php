<?php

namespace App\Identity\Domain\ValueObject;

enum Role: string
{
    case USER = 'ROLE_USER';
    case ADMIN = 'ROLE_ADMIN';
}
