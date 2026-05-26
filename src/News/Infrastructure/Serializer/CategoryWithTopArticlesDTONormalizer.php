<?php

namespace App\News\Infrastructure\Serializer;

use App\News\Application\Query\Handler\DTO\CategoryWithTopArticlesDTO;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Top-news projection: category id+title plus its nested top articles. Each
 * inner ArticleDTO is normalized via {@see ArticleDTONormalizer} which we
 * inject directly to avoid pulling in the whole Symfony serializer (and the
 * circular dependency that comes with it).
 */
final class CategoryWithTopArticlesDTONormalizer implements NormalizerInterface
{
    public function __construct(private readonly ArticleDTONormalizer $articleNormalizer)
    {
    }

    public function normalize($data, ?string $format = null, array $context = []): array
    {
        /** @var CategoryWithTopArticlesDTO $data */
        return [
            'id'       => $data->id,
            'title'    => $data->title,
            'articles' => array_map(
                fn($article) => $this->articleNormalizer->normalize($article, $format, $context),
                $data->articles,
            ),
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof CategoryWithTopArticlesDTO;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [CategoryWithTopArticlesDTO::class => true];
    }
}
