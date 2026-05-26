<?php

namespace App\Shared\Infrastructure\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Doctrine SQL filter that automatically appends `deleted_at IS NULL` to any
 * query on an entity exposing a `deletedAt` field. Soft-deleted rows are
 * therefore invisible to read-side queries by default; disable the filter
 * explicitly when you need them.
 */
class SoftDeleteFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if (!$targetEntity->hasField('deletedAt')) {
            return '';
        }

        return sprintf('%s.deleted_at IS NULL', $targetTableAlias);
    }
}
