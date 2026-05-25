<?php

namespace App\News\Application\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class CreateImageCommand
{
    public function __construct(
        public UploadedFile $file,
    ) {
    }
}