<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\RegisterUserCommand;
use App\News\Domain\Entity\User;
use App\News\Domain\Exception\EmailAlreadyTakenException;
use App\News\Domain\Repository\UserRepositoryInterface;
use App\News\Domain\ValueObject\Email;
use App\News\Domain\ValueObject\HashedPassword;
use App\News\Domain\ValueObject\UserID;

class RegisterUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(RegisterUserCommand $command): UserID
    {
        $email = new Email($command->email);

        if ($this->userRepository->findByEmail($email) !== null) {
            throw EmailAlreadyTakenException::for($email);
        }

        $user = User::register($email, new HashedPassword($command->hashedPassword));

        return $this->userRepository->save($user);
    }
}
