<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\CreateImageCommand;
use App\News\Domain\Entity\Image;
use App\News\Domain\Repository\ImageRepositoryInterface;
use App\News\Domain\ValueObject\ImageID;
use Symfony\Component\String\Slugger\SluggerInterface;

class CreateImageHandler
{
    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    /**
     * @var ImageRepositoryInterface
     */
    private ImageRepositoryInterface $imageRepository;

    private string $imagesDirectory;

    public function __construct(string $imagesDirectory, SluggerInterface $slugger, ImageRepositoryInterface $imageRepository) {
        $this->slugger = $slugger;
        $this->imageRepository = $imageRepository;
        $this->imagesDirectory = $imagesDirectory;
    }

    public function __invoke(CreateImageCommand $command): Image
    {
        $originalFilename = pathinfo($command->file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $command->file->guessExtension();

        $command->file->move($this->imagesDirectory, $newFilename);

        $image = new Image();
        $image->setFilename($newFilename)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        $this->imageRepository->save($image);

        return $image;
    }
}