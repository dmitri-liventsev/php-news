<?php

namespace App\News\Application\Command;

use App\News\Interface\Http\Admin\Controller\Request\CreateImageRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class CreateImageCommand
{
    public function __construct(
        public UploadedFile $file,
    ) {}

    public static function fromRequest(CreateImageRequest $request): CreateImageCommand {
        return new self($request->getFile());
    }
}