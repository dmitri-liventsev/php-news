<?php

namespace App\Identity\Application\Command\Handler;

use App\Identity\Application\Command\RegisterUserCommand;
use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Exception\EmailAlreadyTakenException;
use App\Identity\Domain\Repository\UserRepositoryInterface;
use App\Identity\Domain\ValueObject\Email;
use App\Identity\Domain\ValueObject\HashedPassword;
use App\Identity\Domain\ValueObject\UserID;

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
