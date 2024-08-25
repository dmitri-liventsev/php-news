<?php

namespace App\News\Domain\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CategoryIDs extends Constraint
{
    public $message = 'The category IDs "{{ value }}" are not valid.';
}