<?php

namespace App\News\Domain\Validator\Constraints;

use App\News\Domain\Repository\CategoryRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CategoryIDsValidator extends ConstraintValidator
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        if (!is_array($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
            return;
        }

        $existingCategories = $this->categoryRepository->findByIds($value);
        $existingCategoryIds = array_map(fn($category) => $category->getId(), $existingCategories);

        $invalidIds = array_diff($value, $existingCategoryIds);

        if (!empty($invalidIds)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', implode(', ', $invalidIds))
                ->addViolation();
        }
    }
}

