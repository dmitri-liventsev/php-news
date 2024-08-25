<?php

namespace App\News\Domain\Validator\Constraints;

use App\News\Domain\Repository\ImageRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ImageIDValidator extends ConstraintValidator
{
    private ImageRepositoryInterface $imageRepository;

    public function __construct(ImageRepositoryInterface $categoryRepository)
    {
        $this->imageRepository = $categoryRepository;
    }

    /**
     * @param $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        if (!is_int($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
            return;
        }

        $existedImage = $this->imageRepository->findById($value);
        if (empty($existedImage)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}