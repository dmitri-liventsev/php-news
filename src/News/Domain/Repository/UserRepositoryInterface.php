<?php

namespace App\News\Domain\Repository;

use App\News\Domain\Entity\User;
use App\News\Domain\ValueObject\Email;
use App\News\Domain\ValueObject\UserID;

interface UserRepositoryInterface
{
    public function findById(UserID $userID): ?User;

    public function findByEmail(Email $email): ?User;

    public function save(User $user): UserID;
}
