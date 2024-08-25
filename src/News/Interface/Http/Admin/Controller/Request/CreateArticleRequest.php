<?php

namespace App\News\Interface\Http\Admin\Controller\Request;

use App\News\Domain\Validator\Constraints\CategoryIDs;
use App\News\Domain\Validator\Constraints\ImageID;
use App\News\Infrastructure\Util\Request\BaseRequest;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;

class CreateArticleRequest extends BaseRequest
{
    protected string $title;

    protected string $shortDescription;

    protected string $content;

    protected int $imageID;

    protected array $categories;

    public function getRules(): array
    {
        return [
            'title' => [
                new NotBlank(),
                new Type('string')
            ],
            'shortDescription' => [
                new NotBlank(),
                new Type('string')
            ],
            'content' => [
                new NotBlank(),
                new Type('string')
            ],
            'imageID' => [
                new NotBlank(),
                new Type('integer'),
                new Positive(),
                new ImageID()
            ],
            'categories' => [
                new NotBlank(),
                new Type('array'),
                new CategoryIDs(),
                new All([
                    new Type('integer'),
                    new Positive()
                ])
            ]
        ];
    }
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getImageID(): ?string
    {
        return $this->imageID;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }
}