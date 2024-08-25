<?php

namespace App\News\Infrastructure\Serializer;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CategoryIdNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = []): float|int|bool|\ArrayObject|array|string|null
    {
        if (!$object instanceof Collection) {
            throw new \InvalidArgumentException('Object must be an instance of Doctrine\Common\Collections\Collection.');
        }

        return array_map(fn($category) => $category->getId()->getValue(), $object->toArray());
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