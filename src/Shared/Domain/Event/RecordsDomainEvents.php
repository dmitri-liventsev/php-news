<?php

namespace App\Shared\Domain\Event;

interface RecordsDomainEvents
{
    /**
     * Return the accumulated events and clear the buffer.
     *
     * @return object[]
     */
    public function pullEvents(): array;
}
