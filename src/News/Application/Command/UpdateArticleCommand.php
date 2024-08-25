<?php

namespace App\News\Application\Command;

use App\News\Domain\ValueObject\ArticleID;
use App\News\Interface\Http\Admin\Controller\Request\UpdateArticleRequest;

readonly class UpdateArticleCommand
{
    public function __construct(
        public ArticleID $articleID,
        public string $title,
        public string $shortDescription,
        public string $content,
        public int    $imageID,
        public array  $categories
    ) {}


    public static function fromRequest(int $articleID, UpdateArticleRequest $request): UpdateArticleCommand {
        $articleID = new ArticleID($articleID);

        return new self(
            $articleID,
            $request->getTitle(),
            $request->getShortDescription(),
            $request->getContent(),
            $request->getImageID(),
            $request->getCategories()
        );
    }
}