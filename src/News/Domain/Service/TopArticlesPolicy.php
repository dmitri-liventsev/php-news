<?php

namespace App\News\Domain\Service;

use App\News\Domain\Entity\Article;

/**
 * Business rule: "top articles of a category = N most recent ones".
 *
 * Given the current tops and candidates (latest articles sorted by createdAt DESC),
 * promotes the first N candidates via markAsTop and demotes anything that fell
 * out of the window.
 */
final class TopArticlesPolicy
{
    public function __construct(private readonly int $topSize = 3)
    {
    }

    public function size(): int
    {
        return $this->topSize;
    }

    /**
     * @param iterable<Article> $currentTop Articles currently flagged isTop=true in the category.
     * @param iterable<Article> $latest     Candidates sorted by createdAt DESC.
     */
    public function apply(iterable $currentTop, iterable $latest): void
    {
        $newTop = [];
        foreach ($latest as $article) {
            if (count($newTop) >= $this->topSize) {
                break;
            }
            $key = spl_object_hash($article);
            $newTop[$key] = $article;
            $article->markAsTop();
        }

        foreach ($currentTop as $article) {
            if (!isset($newTop[spl_object_hash($article)])) {
                $article->unmarkAsTop();
            }
        }
    }
}