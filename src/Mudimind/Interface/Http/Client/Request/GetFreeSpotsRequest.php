<?php

namespace App\Mudimind\Interface\Http\Client\Request;

use App\Mudimind\Application\Query\GetFreeSpotsQuery;
use App\Mudimind\Domain\ValueObject\MasseurID;
use App\Shared\Infrastructure\Http\Request\BaseRequest;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class GetFreeSpotsRequest extends BaseRequest
{
    protected ?string $date = null;

    protected ?string $duration = null;

    protected ?string $masseur_id = null;

    /**
     * GET endpoint — populate from the query string instead of the JSON body.
     */
    public function fillFromRequest(Request $http): void
    {
        $this->date = $http->query->get('date');
        $this->duration = $http->query->get('duration');
        $this->masseur_id = $http->query->get('masseur_id');
    }

    public function getRules(): array
    {
        return [
            'date' => [
                new NotBlank(),
                new Date(),
            ],
            // null values are skipped by these constraints, so both stay optional.
            'duration' => [
                new Choice(['choices' => ['30', '45', '60', '75', '90', '105', '120']]),
            ],
            'masseur_id' => [
                new Type('digit'),
            ],
        ];
    }

    public function toQuery(): GetFreeSpotsQuery
    {
        return new GetFreeSpotsQuery(
            new DateTimeImmutable($this->date),
            $this->duration !== null ? (int) $this->duration : 60,
            $this->masseur_id !== null ? new MasseurID((int) $this->masseur_id) : null,
        );
    }
}