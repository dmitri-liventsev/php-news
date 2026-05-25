<?php

namespace App\News\Interface\Http\Admin\Controller\Request;

use App\News\Application\Command\CreateCategoryCommand;
use App\News\Infrastructure\Util\Request\BaseRequest;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class CreateCategoryRequest extends BaseRequest
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

    public function toCommand(): CreateCategoryCommand
    {
        return new CreateCategoryCommand($this->getTitle());
    }
}