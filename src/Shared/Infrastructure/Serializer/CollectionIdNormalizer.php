<?php

namespace App\Shared\Infrastructure\Serializer;

use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes a Doctrine Collection into a flat array of ids. Expects each
 * element to expose `getId(): { value: scalar }` (the project-wide VO id shape).
 *
 * Plug in per-attribute via serializer YAML, e.g.:
 *   App\News\Domain\Entity\Article:
 *     attributes:
 *       categories:
 *         normalizer: App\Shared\Infrastructure\Serializer\CollectionIdNormalizer
 */
class CollectionIdNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = []): float|int|bool|\ArrayObject|array|string|null
    {
        if (!$object instanceof Collection) {
            throw new InvalidArgumentException('Object must be an instance of Doctrine\Common\Collections\Collection.');
        }

        return array_map(fn($entity) => $entity->getId()->value, $object->toArray());
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof Collection;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Collection::class];
    }
}
