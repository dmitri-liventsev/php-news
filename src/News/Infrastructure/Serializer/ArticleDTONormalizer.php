<?php

namespace App\News\Infrastructure\Serializer;

use App\News\Application\Query\Handler\DTO\ArticleDTO;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Picks the public-facing fields of {@see ArticleDTO}. Internal-only fields
 * (createdAt / updatedAt / deletedAt) are intentionally omitted — they remain
 * available on the DTO for application-side use but are not part of the wire
 * contract.
 */
final class ArticleDTONormalizer implements NormalizerInterface
{
    public function normalize($data, ?string $format = null, array $context = []): array
    {
        /** @var ArticleDTO $data */
        return [
            'id'               => $data->id,
            'title'            => $data->title,
            'shortDescription' => $data->shortDescription,
            'content'          => $data->content,
            'image'            => $data->image,
            'numberOfViews'    => $data->numberOfViews,
            'isTop'            => $data->isTop,
            'categories'       => $data->categories,
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ArticleDTO;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [ArticleDTO::class => true];
    }
}
