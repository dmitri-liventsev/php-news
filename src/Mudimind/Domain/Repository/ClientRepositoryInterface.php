<?php

namespace App\Mudimind\Domain\Repository;

use App\Mudimind\Domain\Entity\Client;
use App\Mudimind\Domain\ValueObject\Email;

interface ClientRepositoryInterface
{
    public function findByEmail(Email $email): ?Client;

    public function save(Client $client): Client;
}
