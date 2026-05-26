<?php

namespace App\Shared\Domain\Event;

trait RecordsDomainEventsTrait
{
    /** @var object[] */
    private array $recordedEvents = [];

    protected function recordThat(object $event): void
    {
        $this->recordedEvents[] = $event;
    }

    public function pullEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }
}
