<?php

namespace App\News\Interface\Http\Client\Controller\Request;

use App\News\Application\Command\CreateCommentCommand;
use App\News\Domain\ValueObject\ArticleID;
use App\Shared\Infrastructure\Http\Request\BaseRequest;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class CreateCommentRequest extends BaseRequest
{
    protected string $content;

    protected string $author;

    public function getRules(): array
    {
        return [
            'content' => [
                new NotBlank(),
                new Type('string')
            ],
            'author' => [
                new NotBlank(),
                new Type('string'),
                new Length([
                    'min' => 2,
                    'max' => 250,
                    'minMessage' => 'Author name must be at least {{ limit }} characters long',
                    'maxMessage' => 'Author author name cannot be longer than {{ limit }} characters',
                ])
            ],
        ];
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function toCommand(int $articleID): CreateCommentCommand
    {
        return new CreateCommentCommand(
            new ArticleID($articleID),
            $this->getContent(),
            $this->getAuthor(),
        );
    }
}