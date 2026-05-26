<?php

namespace App\Shared\Domain\Event;

use DateTimeImmutable;

/**
 * Marker + tracing contract for every domain event.
 *
 *   $event->eventId()    — UUID v4, unique per emission; correlate across logs.
 *   $event->occurredOn() — wall-clock at the moment the aggregate recorded it.
 *
 * Both are set in the constructor of {@see AbstractDomainEvent}; concrete
 * events only need to extend that base and call `parent::__construct()`.
 */
interface DomainEvent
{
    public function eventId(): string;

    public function occurredOn(): DateTimeImmutable;
}
