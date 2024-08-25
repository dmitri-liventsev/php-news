<?php

namespace App\News\Application\Query\Handler\DTO;

use App\News\Domain\Entity\Article;

class ArticleDTO
{
    public int $id;
    public string $title;
    public string $shortDescription;
    public string $content;
    public array $image;
    public int $numberOfViews;
    public bool $isTop;
    public array $categories;
    public string $createdAt;
    public string $updatedAt;
    public ?string $deletedAt;

    public function __construct(Article $article)
    {
        $this->id = $article->getId()->getValue();
        $this->title = $article->getTitle();
        $this->shortDescription = $article->getShortDescription();
        $this->content = $article->getContent();
        $this->image = ["id" => $article->getImage()?->getId()->getValue(), "fileName" => $article->getImage()?->getFileName()];
        $this->numberOfViews = $article->getNumberOfViews();
        $this->isTop = $article->getIsTop();
        $this->categories = array_map(
            fn($category) => ["id" => $category->getId()->getValue(), "title" => $category->getTitle()],
            $article->getCategories()->toArray()
        );
        $this->createdAt = $article->getCreatedAt()->format('c');
        $this->updatedAt = $article->getUpdatedAt()->format('c');
        $this->deletedAt = $article->getDeletedAt() ? $article->getDeletedAt()->format('c') : null;
    }
}