<?php

namespace App\News\Application\Command;

use App\News\Interface\Http\Admin\Controller\Request\CreateArticleRequest;

readonly class CreateArticleCommand
{
    public function __construct(
        public string $title,
        public string $shortDescription,
        public string $content,
        public int    $imageID,
        public array  $categories
    ) {}

    public static function fromRequest(CreateArticleRequest $request): CreateArticleCommand {
        return new self(
            $request->getTitle(),
            $request->getShortDescription(),
            $request->getContent(),
            $request->getImageID(),
            $request->getCategories()
        );
    }
}