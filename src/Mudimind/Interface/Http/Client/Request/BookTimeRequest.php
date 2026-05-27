<?php

namespace App\Mudimind\Interface\Http\Client\Request;

use App\Mudimind\Application\Command\BookTimeCommand;
use App\Mudimind\Domain\ValueObject\Email;
use App\Mudimind\Domain\ValueObject\MasseurID;
use App\Shared\Infrastructure\Http\Request\BaseRequest;
use DateTime;
use Symfony\Component\Validator\Constraints\DateTime as DateTimeConstraint;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class BookTimeRequest extends BaseRequest
{
    protected ?string $started_at = null;

    protected ?string $end_at = null;

    protected ?int $masseur_id = null;

    protected ?string $email = null;

    public function getRules(): array
    {
        return [
            'started_at' => [
                new NotBlank(),
                new DateTimeConstraint(['format' => 'Y-m-d H:i']),
            ],
            'end_at' => [
                new NotBlank(),
                new DateTimeConstraint(['format' => 'Y-m-d H:i']),
            ],
            'masseur_id' => [
                new NotBlank(),
                new Type('integer'),
            ],
            'email' => [
                new NotBlank(),
                new EmailConstraint(),
            ],
        ];
    }

    public function toCommand(): BookTimeCommand
    {
        return new BookTimeCommand(
            new DateTime($this->started_at),
            new DateTime($this->end_at),
            new MasseurID((int) $this->masseur_id),
            new Email($this->email),
        );
    }
}