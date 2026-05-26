<?php

namespace App\Identity\Application\Command;

readonly class RegisterUserCommand
{
    /**
     * @param string $hashedPassword Already hashed by Symfony's UserPasswordHasher before dispatch.
     */
    public function __construct(
        public string $email,
        public string $hashedPassword,
    ) {
    }
}
