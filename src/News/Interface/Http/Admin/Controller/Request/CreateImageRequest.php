<?php

namespace App\News\Interface\Http\Admin\Controller\Request;

use App\News\Infrastructure\Util\Request\BaseRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateImageRequest extends BaseRequest
{
    protected UploadedFile $file;

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

    public function getFile() : UploadedFile
    {
        return $this->file;
    }

    protected function populate() : void {
        parent::populate();
        $this->file = $this->getRequest()->files->get('image');
    }
}