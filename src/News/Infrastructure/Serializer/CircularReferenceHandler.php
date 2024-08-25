<?php

namespace App\News\Infrastructure\Serializer;

class CircularReferenceHandler
{
    public function __invoke($object)
    {
        return $object->getId();
    }
}