<?php

namespace App\News\Interface\Http\Admin\Controller\Request;

use App\News\Application\Command\UpdateCategoryCommand;
use App\News\Domain\ValueObject\CategoryID;
use App\Shared\Infrastructure\Http\Request\BaseRequest;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class UpdateCategoryRequest extends BaseRequest
{
    protected string $title;

    public function getRules(): array
    {
        return [
            'title' => [
                new NotBlank(),
                new Type('string')
            ],
        ];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function toCommand(int $categoryID): UpdateCategoryCommand
    {
        return new UpdateCategoryCommand(new CategoryID($categoryID), $this->getTitle());
    }
}