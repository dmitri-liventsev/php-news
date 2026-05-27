<?php

namespace App\Mudimind\Application\Command\Handler;

use App\Mudimind\Application\Command\BookTimeCommand;
use App\Mudimind\Domain\Entity\Book;
use App\Mudimind\Domain\Entity\Client;
use App\Mudimind\Domain\Exception\MasseurNotFoundException;
use App\Mudimind\Domain\Exception\SpotNotAvailableException;
use App\Mudimind\Domain\Repository\BookRepositoryInterface;
use App\Mudimind\Domain\Repository\ClientRepositoryInterface;
use App\Mudimind\Domain\Repository\MasseurRepositoryInterface;
use App\Mudimind\Domain\Service\FreeSpotPolicy;
use App\Mudimind\Domain\ValueObject\BookID;

class BookTimeHandler
{
    public function __construct(
        private readonly MasseurRepositoryInterface $masseurRepository,
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly BookRepositoryInterface $bookRepository,
        private readonly FreeSpotPolicy $freeSpotPolicy,
    ) {
    }

    public function __invoke(BookTimeCommand $command): BookID
    {
        $masseur = $this->masseurRepository->findById($command->masseurID);
        if ($masseur === null) {
            throw MasseurNotFoundException::byId($command->masseurID);
        }

        $busy = array_map(
            static fn (Book $book): array => [
                'start' => $book->getStartAt(),
                'end' => $book->getEndAt(),
            ],
            $this->bookRepository->findByMasseurAndDay($command->masseurID, $command->startedAt),
        );

        if (!$this->freeSpotPolicy->isAvailable($command->startedAt, $command->endedAt, $busy)) {
            throw SpotNotAvailableException::forMasseur($command->masseurID, $command->startedAt, $command->endedAt);
        }

        $client = $this->clientRepository->findByEmail($command->email)
            ?? $this->clientRepository->save(Client::register($command->email));

        $book = Book::create($masseur, $client, $command->startedAt, $command->endedAt);

        return $this->bookRepository->save($book);
    }
}