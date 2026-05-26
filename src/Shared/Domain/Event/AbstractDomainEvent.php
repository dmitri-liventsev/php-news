<?php

namespace App\Shared\Domain\Event;

use DateTimeImmutable;

abstract readonly class AbstractDomainEvent implements DomainEvent
{
    public string $eventId;

    public DateTimeImmutable $occurredOn;

    public function __construct()
    {
        $this->eventId = self::generateEventId();
        $this->occurredOn = new DateTimeImmutable();
    }

    final public function eventId(): string
    {
        return $this->eventId;
    }

    final public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    /** RFC 4122 v4 UUID; no external dependency. */
    private static function generateEventId(): string
    {
        $b = random_bytes(16);
        $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
    }
}
