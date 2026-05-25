<?php

namespace App\News\Infrastructure\Util\Request;

use ReflectionProperty;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class BaseRequestValueResolver implements ValueResolverInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();
        if ($type === null || !is_subclass_of($type, BaseRequest::class)) {
            return [];
        }

        /** @var BaseRequest $dto */
        $dto = new $type();
        $dto->fillFromRequest($request);
        $this->validate($dto);

        yield $dto;
    }

    private function validate(BaseRequest $dto): void
    {
        $rules = $dto->getRules();
        $data = [];
        foreach ($rules as $field => $_) {
            $property = new ReflectionProperty($dto, $field);
            $data[$field] = $property->isInitialized($dto) ? $property->getValue($dto) : null;
        }

        $violations = $this->validator->validate($data, new Collection($rules));

        if (count($violations) > 0) {
            throw new ValidationFailedException($dto, $violations);
        }
    }
}