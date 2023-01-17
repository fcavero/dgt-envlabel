<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\Filesystem\Exception\RuntimeException;

class FilesystemException extends RuntimeException
{
    public static function fromDirectoryCreation(string $dirPath, int $code, string $message): self
    {
        throw new self(\sprintf('Creation of %s directory went wrong [ %s → %s ]', $dirPath, $code, $message));
    }

    public static function fileDoesNotExist(string $filepath): self
    {
        throw new self(\sprintf('The file %s does not exist.', $filepath));
    }

    public static function emptyDirectory(string $filepath): self
    {
        throw new self(\sprintf('The directory %s is empty.', $filepath));
    }

    public static function fromUnzipProcess(string $message): self
    {
        throw new self($message);
    }

}
