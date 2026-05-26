<?php

namespace App\Shared\Domain;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Soft-delete capability for an aggregate root. Filling `deleted_at` lets the
 * Doctrine SoftDeleteFilter hide the row from ORM queries; DBAL finders apply
 * the same condition manually.
 *
 * Idempotent — calling {@see softDelete()} twice is a no-op.
 *
 * Expects the using entity to also `use Timestamped` so `updatedAt` is bumped
 * on deletion; if it doesn't, the trait still works but `updatedAt` won't move.
 */
trait SoftDeletable
{
    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $deletedAt = null;

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function softDelete(): void
    {
        if ($this->deletedAt !== null) {
            return;
        }
        $this->deletedAt = new DateTime();
        if (method_exists($this, 'touch')) {
            $this->touch();
        }
    }
}
