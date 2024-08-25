<?php

namespace App\News\Application\Command;

use App\News\Interface\Http\Admin\Controller\Request\CreateCategoryRequest;

readonly class CreateCategoryCommand
{
    public function __construct(
        public string $title,
    ) {}

    public static function fromRequest(CreateCategoryRequest $request): CreateCategoryCommand {
        return new self($request->getTitle());
    }
}