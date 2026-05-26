<?php

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\User;
use App\Identity\Domain\ValueObject\Email;
use App\Identity\Domain\ValueObject\UserID;

interface UserRepositoryInterface
{
    public function findById(UserID $userID): ?User;

    public function findByEmail(Email $email): ?User;

    public function save(User $user): UserID;
}
