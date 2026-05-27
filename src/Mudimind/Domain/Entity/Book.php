<?php

namespace App\Mudimind\Domain\Entity;

use App\Mudimind\Domain\Event\BookTimeCreated;
use App\Mudimind\Domain\ValueObject\BookID;
use App\Shared\Domain\Event\RecordsDomainEvents;
use App\Shared\Domain\Event\RecordsDomainEventsTrait;
use App\Shared\Domain\SoftDeletable;
use App\Shared\Domain\Timestamped;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * Booking aggregate root: a single massage appointment for one client with one
 * masseur over a half-open time interval [startAt, endAt).
 */
#[ORM\Entity]
#[ORM\Table(name: 'book')]
#[ORM\HasLifecycleCallbacks]
class Book implements RecordsDomainEvents
{
    use RecordsDomainEventsTrait;
    use Timestamped;
    use SoftDeletable;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'start_at', type: 'datetime')]
    private DateTimeInterface $startAt;

    #[ORM\Column(name: 'end_at', type: 'datetime')]
    private DateTimeInterface $endAt;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id', nullable: false)]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: Masseur::class)]
    #[ORM\JoinColumn(name: 'masseur_id', referencedColumnName: 'id', nullable: false)]
    private Masseur $masseur;

    private function __construct()
    {
    }

    public static function create(
        Masseur $masseur,
        Client $client,
        DateTimeInterface $startAt,
        DateTimeInterface $endAt,
    ): self {
        if ($endAt <= $startAt) {
            throw new InvalidArgumentException('Booking end time must be after its start time.');
        }

        $book = new self();
        $book->masseur = $masseur;
        $book->client = $client;
        $book->startAt = $startAt;
        $book->endAt = $endAt;
        $book->initTimestamps();

        return $book;
    }

    #[ORM\PostPersist]
    public function onPersisted(): void
    {
        $masseurID = $this->masseur->getId();
        if ($this->id === null || $masseurID === null) {
            return;
        }

        $this->recordThat(new BookTimeCreated(
            new BookID($this->id),
            $masseurID,
            DateTimeImmutable::createFromInterface($this->startAt),
            DateTimeImmutable::createFromInterface($this->endAt),
        ));
    }

    public function getId(): ?BookID
    {
        return $this->id !== null ? new BookID($this->id) : null;
    }

    public function getStartAt(): DateTimeInterface
    {
        return $this->startAt;
    }

    public function getEndAt(): DateTimeInterface
    {
        return $this->endAt;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getMasseur(): Masseur
    {
        return $this->masseur;
    }
}