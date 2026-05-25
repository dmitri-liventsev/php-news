<?php

namespace App\News\Application\Query\Handler\DTO;

use App\News\Domain\Entity\Article;
use JsonSerializable;

class ArticleDTO implements JsonSerializable
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
        $this->id = $article->getId()->value;
        $this->title = $article->getTitle()->value;
        $this->shortDescription = $article->getShortDescription()->value;
        $this->content = $article->getContent()->value;
        $this->image = ["id" => $article->getImage()?->getId()->value, "fileName" => $article->getImage()?->getFileName()->value];
        $this->numberOfViews = $article->getNumberOfViews();
        $this->isTop = $article->isTop();
        $this->categories = array_map(
            fn($category) => ["id" => $category->getId()->value, "title" => $category->getTitle()->value],
            $article->getCategories()->toArray()
        );
        $this->createdAt = $article->getCreatedAt()->format('c');
        $this->updatedAt = $article->getUpdatedAt()->format('c');
        $this->deletedAt = $article->getDeletedAt()?->format('c');
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'shortDescription' => $this->shortDescription,
            'content' => $this->content,
            'image' => $this->image,
            'numberOfViews' => $this->numberOfViews,
            'isTop' => $this->isTop,
            'categories' => $this->categories
        ];
    }
}