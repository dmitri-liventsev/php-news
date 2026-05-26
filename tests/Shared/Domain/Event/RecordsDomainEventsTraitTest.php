<?php

namespace App\Tests\Shared\Domain\Event;

use App\Shared\Domain\Event\RecordsDomainEvents;
use App\Shared\Domain\Event\RecordsDomainEventsTrait;
use PHPUnit\Framework\TestCase;
use stdClass;

class RecordsDomainEventsTraitTest extends TestCase
{
    public function testPullReturnsAndClearsAccumulatedEvents(): void
    {
        $aggregate = new class implements RecordsDomainEvents {
            use RecordsDomainEventsTrait;

            public function trigger(object $event): void
            {
                $this->recordThat($event);
            }
        };

        $a = new stdClass();
        $b = new stdClass();
        $aggregate->trigger($a);
        $aggregate->trigger($b);

        $this->assertSame([$a, $b], $aggregate->pullEvents());
        $this->assertSame([], $aggregate->pullEvents(), 'pullEvents must clear the buffer');
    }
}
