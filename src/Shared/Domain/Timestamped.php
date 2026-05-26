<?php

namespace App\Shared\Domain;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Adds `createdAt` / `updatedAt` to an aggregate. Call {@see initTimestamps()}
 * once from the static factory; call {@see touch()} from any mutating method
 * to bump `updatedAt`.
 *
 * Co-located with {@see SoftDeletable} on every entity that has `deleted_at`.
 */
trait Timestamped
{
    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private DateTimeInterface $updatedAt;

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    protected function initTimestamps(): void
    {
        $now = new DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    protected function touch(): void
    {
        $this->updatedAt = new DateTime();
    }
}
