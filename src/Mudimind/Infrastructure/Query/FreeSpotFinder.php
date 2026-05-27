<?php

namespace App\Mudimind\Infrastructure\Query;

use App\Mudimind\Application\Query\Finder\FreeSpotFinderInterface;
use App\Mudimind\Application\Query\Handler\DTO\FreeSpotDTO;
use App\Mudimind\Domain\Service\FreeSpotPolicy;
use App\Mudimind\Domain\ValueObject\MasseurID;
use DateTimeImmutable;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

/**
 * Read-side free-spot projector: plain DBAL, no ORM, no entity hydration.
 *
 * Reads masseurs and the day's bookings, then defers to the pure FreeSpotPolicy
 * to turn busy intervals into free slots. Soft-deleted rows are filtered manually
 * with `deleted_at IS NULL`.
 */
final readonly class FreeSpotFinder implements FreeSpotFinderInterface
{
    private const OUTPUT_FORMAT = 'Y-m-d H:i';

    public function __construct(
        private Connection $connection,
        private FreeSpotPolicy $policy,
    ) {
    }

    public function findFreeSpots(DateTimeImmutable $date, int $durationMinutes, ?MasseurID $masseurId): array
    {
        $masseurs = $this->loadMasseurs($masseurId);
        if ($masseurs === []) {
            return [];
        }

        $busyByMasseur = $this->loadBusyIntervals(array_keys($masseurs), $date);

        $spots = [];
        foreach ($masseurs as $id => $name) {
            $busy = $busyByMasseur[$id] ?? [];
            foreach ($this->policy->freeSlots($date, $durationMinutes, $busy) as $slot) {
                $spots[] = new FreeSpotDTO(
                    $id,
                    $name,
                    $slot['start']->format(self::OUTPUT_FORMAT),
                    $slot['end']->format(self::OUTPUT_FORMAT),
                );
            }
        }

        return $spots;
    }

    /**
     * @return array<int, string> masseur id => name
     */
    private function loadMasseurs(?MasseurID $masseurId): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('m.id', 'm.name')
            ->from('masseur', 'm')
            ->where('m.deleted_at IS NULL')
            ->orderBy('m.id', 'ASC');

        if ($masseurId !== null) {
            $qb->andWhere('m.id = :id')->setParameter('id', $masseurId->value);
        }

        $masseurs = [];
        foreach ($qb->executeQuery()->fetchAllAssociative() as $row) {
            $masseurs[(int) $row['id']] = (string) $row['name'];
        }

        return $masseurs;
    }

    /**
     * @param int[] $masseurIds
     *
     * @return array<int, list<array{start: DateTimeImmutable, end: DateTimeImmutable}>>
     */
    private function loadBusyIntervals(array $masseurIds, DateTimeImmutable $date): array
    {
        $dayStart = $date->setTime(0, 0);
        $nextDay = $dayStart->modify('+1 day');

        $rows = $this->connection->createQueryBuilder()
            ->select('b.masseur_id', 'b.start_at', 'b.end_at')
            ->from('book', 'b')
            ->where('b.deleted_at IS NULL')
            ->andWhere('b.masseur_id IN (:ids)')
            ->andWhere('b.start_at >= :dayStart')
            ->andWhere('b.start_at < :nextDay')
            ->setParameter('ids', $masseurIds, ArrayParameterType::INTEGER)
            ->setParameter('dayStart', $dayStart->format('Y-m-d H:i:s'))
            ->setParameter('nextDay', $nextDay->format('Y-m-d H:i:s'))
            ->executeQuery()
            ->fetchAllAssociative();

        $busy = [];
        foreach ($rows as $row) {
            $busy[(int) $row['masseur_id']][] = [
                'start' => new DateTimeImmutable((string) $row['start_at']),
                'end' => new DateTimeImmutable((string) $row['end_at']),
            ];
        }

        return $busy;
    }
}