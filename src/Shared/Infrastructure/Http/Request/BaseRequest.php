<?php

namespace App\Shared\Infrastructure\Http\Request;

use ReflectionProperty;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseRequest
{
    /**
     * @return array<string, array<int, \Symfony\Component\Validator\Constraint>>
     */
    abstract public function getRules(): array;

    /**
     * Hook for populating properties from the incoming HTTP request.
     * The default implementation maps the JSON body onto class properties by name;
     * subclasses override it for multipart uploads or other custom payloads.
     */
    public function fillFromRequest(Request $http): void
    {
        try {
            $data = $http->getContent() !== '' ? $http->toArray() : [];
        } catch (\JsonException) {
            $data = [];
        }

        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                (new ReflectionProperty($this, $property))->setValue($this, $value);
            }
        }
    }
}
