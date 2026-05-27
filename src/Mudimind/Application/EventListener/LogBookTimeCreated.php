<?php

namespace App\Mudimind\Application\EventListener;

use App\Mudimind\Domain\Event\BookTimeCreated;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * Placeholder reaction to a new booking — a real implementation would dispatch a
 * confirmation email / SMS. For now it just records the event for observability.
 */
#[AsEventListener(event: BookTimeCreated::class)]
final class LogBookTimeCreated
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(BookTimeCreated $event): void
    {
        $this->logger->info('Massage booked', [
            'eventId' => $event->eventId(),
            'bookId' => $event->bookId->value,
            'masseurId' => $event->masseurId->value,
            'startAt' => $event->startAt->format(DATE_ATOM),
            'endAt' => $event->endAt->format(DATE_ATOM),
        ]);
    }
}