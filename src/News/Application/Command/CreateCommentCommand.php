<?php

namespace App\News\Application\Command;

use App\News\Domain\ValueObject\ArticleID;
use App\News\Interface\Http\Client\Controller\Request\CreateCommentRequest;

readonly class CreateCommentCommand
{
    public function __construct(
        public ArticleID $newsId,
        public string    $content,
        public string    $author,
    ) {}

    public static function fromRequest(int $articleID, CreateCommentRequest $request): CreateCommentCommand {
        $articleID = new ArticleID($articleID);

        return new self($articleID, $request->getContent(), $request->getAuthor());
    }
}