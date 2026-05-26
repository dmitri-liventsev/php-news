<?php

namespace App\Shared\Infrastructure\Serializer;

/**
 * Wired into the symfony serializer as the global circular_reference_handler.
 * On a cycle, replaces the offending object with its id — assumes every entity
 * passed to the serializer exposes a `getId()` method.
 */
class CircularReferenceHandler
{
    public function __invoke($object)
    {
        return $object->getId();
    }
}
