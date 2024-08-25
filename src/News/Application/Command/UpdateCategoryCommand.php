<?php

namespace App\News\Application\Command;

use App\News\Domain\ValueObject\CategoryID;
use App\News\Interface\Http\Admin\Controller\Request\UpdateCategoryRequest;

readonly class UpdateCategoryCommand
{
    public function __construct(
        public CategoryID $categoryID,
        public string $title,
    ) {}

    public static function fromRequest(int $categoryID, UpdateCategoryRequest $request): UpdateCategoryCommand {
        $categoryID = new CategoryID($categoryID);

        return new self($categoryID, $request->getTitle());
    }
}