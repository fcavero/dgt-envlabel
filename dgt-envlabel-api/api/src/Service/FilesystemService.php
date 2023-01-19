<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\FilesystemException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class FilesystemService
{
    private LoggerInterface $logger;


    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createDir(string $dirPath): bool
    {
        $filesystem = new Filesystem();

        if ($filesystem->exists($dirPath)) {
            $this->logger->debug(sprintf('%s directory already exists!', $dirPath));
            return true;
        }

        try {
            $filesystem->mkdir(
                Path::normalize($dirPath),
            );

            $this->logger->info(sprintf('%s directory has been created successfully.', $dirPath));

            return true;

        } catch (IOExceptionInterface $exception) {
            throw FilesystemException::fromDirectoryCreation($dirPath, $exception->getCode(), $exception->getMessage());
        }
    }

    public function checkIfExists(string $filepath): bool
    {
        clearstatcache();
        return (file_exists($filepath)
            && is_file($filepath)
            && filesize($filepath) > 0);
    }

    public function removeSingleFile(string $filePath): bool
    {
        $filesystem = new Filesystem();
        $filesystem->remove($filePath);

        if ($this->checkIfExists($filePath)) {
            $this->logger->error(sprintf('Error trying to remove %s file!', $filePath));
            return false;
        }

        $this->logger->info(sprintf('%s file has been removed successfully!', $filePath));
        return true;
    }

}
