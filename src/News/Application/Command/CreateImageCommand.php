<?php

namespace App\News\Application\Command;

use App\Shared\Domain\ValueObject\BinaryFile;

readonly class CreateImageCommand
{
    public function __construct(
        public BinaryFile $file,
    ) {
    }
}
