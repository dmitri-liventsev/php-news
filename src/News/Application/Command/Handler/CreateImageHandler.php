<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\CreateImageCommand;
use App\News\Domain\Entity\Image;
use App\News\Domain\Repository\ImageRepositoryInterface;
use App\News\Domain\ValueObject\ImageFileName;
use Symfony\Component\String\Slugger\SluggerInterface;

class CreateImageHandler
{
    public function __construct(
        private readonly string $imagesDirectory,
        private readonly SluggerInterface $slugger,
        private readonly ImageRepositoryInterface $imageRepository,
    ) {
    }

    public function __invoke(CreateImageCommand $command): Image
    {
        $file = $command->file;
        $safeStem = $this->slugger->slug($file->basename());
        $extension = $file->extension() !== '' ? '.' . $file->extension() : '';
        $newFilename = $safeStem . '-' . uniqid() . $extension;

        if (!is_dir($this->imagesDirectory) && !mkdir($this->imagesDirectory, 0775, true) && !is_dir($this->imagesDirectory)) {
            throw new \RuntimeException(sprintf('Unable to create images directory "%s".', $this->imagesDirectory));
        }

        if (file_put_contents($this->imagesDirectory . '/' . $newFilename, $file->contents) === false) {
            throw new \RuntimeException(sprintf('Failed to write image to "%s".', $this->imagesDirectory));
        }

        $image = Image::create(new ImageFileName($newFilename));
        $this->imageRepository->save($image);

        return $image;
    }
}
