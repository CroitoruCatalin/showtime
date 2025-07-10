<?php

namespace App\Service;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageService
{
    public function __construct(
        private string           $targetDir,
        private SluggerInterface $slugger,
        private ImageRepository  $imageRepo,
        private Filesystem       $filesystem,
    )
    {
    }

    public function upload(UploadedFile $file): Image
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $uniqueName = sprintf('%s-%s.%s', $safeFilename, uniqid(), $file->guessExtension());
        $relativePath = $uniqueName;
        $destPath = rtrim($this->targetDir, '/\\') . '/' . $relativePath;

        $image = new Image();
        try {
            $file->move(dirname($destPath), basename($destPath));

        } catch (FileException $e) {
            throw new \RuntimeException('Failed to move uploaded file.');
        }

        $image = (new Image())
            ->setFilename($relativePath)
            ->setDateTimeCreated(new \DateTime());

        $this->imageRepo->save($image);
        return $image;
    }

    public function removeFile(string $relativePath): void
    {
        $fileName = ltrim($relativePath, '/\\');
        $fullPath = rtrim($this->targetDir, '/\\') . '/' . $fileName;
        $this->filesystem->remove($fullPath);
    }

    public function remove(Image $image): void
    {
        $fileName = ltrim(str_replace('images/', '', $image->getFilename()), '/\\');
        $fullPath = rtrim($this->targetDir, '/\\') . '/' . $fileName;
        $this->filesystem->remove($fullPath);

        $this->imageRepo->delete($image);
    }

    public function getTargetDir(): string
    {
        return $this->targetDir;
    }
}
