<?php

namespace App\News\Interface\Http\Admin\Controller\Request;

use App\News\Application\Command\CreateImageCommand;
use App\Shared\Infrastructure\Http\Request\BaseRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateImageRequest extends BaseRequest
{
    protected ?UploadedFile $file = null;

    public function getRules(): array
    {
        return [
            'file' => [
                new NotBlank(),
                new Image([
                    'maxSize' => '5M',
                    'mimeTypes' => ['image/jpeg', 'image/png'],
                    'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image.',
                ])
            ]
        ];
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function toCommand(): CreateImageCommand
    {
        return new CreateImageCommand($this->getFile());
    }

    public function fillFromRequest(Request $http): void
    {
        $this->file = $http->files->get('image');
    }
}