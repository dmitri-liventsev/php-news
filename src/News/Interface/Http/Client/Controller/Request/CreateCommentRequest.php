<?php

namespace App\News\Interface\Http\Client\Controller\Request;

use App\News\Infrastructure\Util\Request\BaseRequest;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class CreateCommentRequest extends BaseRequest
{
    protected string $content;

    protected string $author;


    protected function getRules(): array
    {
        return [
            'content' => [
                new NotBlank(),
                new Type('string')
            ],
            'author' => [
                new NotBlank(),
                new Type('string')
            ],
        ];
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }
}