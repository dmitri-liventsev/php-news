<?php

namespace App\News\Domain\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ImageID extends Constraint
{
    public $message = 'The image ID "{{ value }}" are not valid.';
}