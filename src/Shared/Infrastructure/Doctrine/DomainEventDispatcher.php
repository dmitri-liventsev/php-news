<?php

namespace App\Shared\Infrastructure\Doctrine;

use App\Shared\Domain\Event\RecordsDomainEvents;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Collects domain events from aggregates and publishes them via Symfony EventDispatcher.
 *
 * onFlush  — tracks which aggregate roots were touched (inserted/updated).
 * postFlush — pulls accumulated events from those aggregates and dispatches them.
 *
 * Re-entrancy guard: if a listener itself triggers a flush, the nested postFlush
 * call returns immediately; newly added entities are still picked up by the
 * while-loop of the outer call.
 */
#[AsDoctrineListener(event: Events::onFlush)]
#[AsDoctrineListener(event: Events::postFlush)]
final class DomainEventDispatcher
{
    /** @var array<string, RecordsDomainEvents> */
    private array $pending = [];

    private bool $dispatching = false;

    public function __construct(private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $uow = $args->getObjectManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->track($entity);
        }
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->track($entity);
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if ($this->dispatching) {
            return;
        }

        $this->dispatching = true;
        try {
            while ($this->pending !== []) {
                $batch = $this->pending;
                $this->pending = [];

                foreach ($batch as $entity) {
                    foreach ($entity->pullEvents() as $event) {
                        $this->eventDispatcher->dispatch($event);
                    }
                }
            }
        } finally {
            $this->dispatching = false;
        }
    }

    private function track(object $entity): void
    {
        if (!$entity instanceof RecordsDomainEvents) {
            return;
        }
        $this->pending[spl_object_hash($entity)] = $entity;
    }
}
