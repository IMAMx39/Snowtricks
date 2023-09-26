<?php

namespace App\Service;

use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class ManagerFile
{


    public function __construct(
        private SluggerInterface $slugger,
        private Filesystem       $filesystem,
        private string           $tricksDir,
        private string           $avatarDir
    )
    {
    }

    private function ensureDirectoryExists(string $directoryPath): void
    {
        if (!$this->filesystem->exists($directoryPath)) {
            $this->filesystem->mkdir($directoryPath);
        }
    }

    private function removeFileIfExists(string $filePath): void
    {
        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
        }
    }

    private function generateUniqueFilename(string $originalFilename): string
    {
        $safeFilename = $this->slugger->slug(pathinfo($originalFilename, PATHINFO_FILENAME));
        return $safeFilename . '-' . uniqid() . '.' . pathinfo($originalFilename, PATHINFO_EXTENSION);
    }

    public function renameTrickPicsDir(string $oldSlug, string $newSlug): void
    {
        $oldDir = $this->tricksDir . $oldSlug;
        $newDir = $this->tricksDir . $newSlug;

        if ($oldDir === $newDir) {
            return;
        }

        if ($this->filesystem->exists($oldDir)) {
            $this->filesystem->rename($oldDir, $newDir);
        }
    }

    /**
     * @throws Exception
     */
    public function uploadTrickImage(UploadedFile $file, string $dirName, string $oldPicName = null): string
    {
        $targetDir = $this->tricksDir . $dirName;
        $this->ensureDirectoryExists($targetDir);

        if ($oldPicName !== null) {
            $this->deleteTrickImage($dirName, $oldPicName);
        }

        return $this->upload($file, $targetDir);
    }

    public function deleteTrickImage(string $trickSlug, string $fileName): void
    {
        $targetPicture = $this->tricksDir . $trickSlug . '/' . $fileName;
        $this->removeFileIfExists($targetPicture);
    }

    /**
     * @throws Exception
     */
    public function uploadAvatar(UploadedFile $file, string $oldAvatarName = null): string
    {
        $targetDir = $this->avatarDir;
        $this->ensureDirectoryExists($targetDir);

        if ($oldAvatarName !== null) {
            $this->deleteAvatar($oldAvatarName);
        }

        return $this->upload($file, $targetDir);
    }

    public function deleteAvatar(string $fileName): void
    {
        $targetDir = $this->avatarDir . $fileName;
        $this->removeFileIfExists($targetDir);
    }


    public function removeTrickPicsDir(string $trickSlug): void
    {
        $targetDir = $this->tricksDir . $trickSlug;
        $this->removeFileIfExists($targetDir);
    }

    /**
     * @throws Exception
     */
    private function upload(UploadedFile $file, string $targetDir): string
    {
        $originalFilename = $file->getClientOriginalName();
        $fileName = $this->generateUniqueFilename($originalFilename);

        try {
            $file->move($targetDir, $fileName);
        } catch (FileException $e) {
            throw new Exception("Exception thrown while moving the file: " . $e->getMessage());
        }

        return $fileName;
    }

}

