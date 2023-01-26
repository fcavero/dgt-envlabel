<?php

declare(strict_types=1);

namespace Settler\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class FilesystemService
{
    private LoggerInterface $logger;


    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
