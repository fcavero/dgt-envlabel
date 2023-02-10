<?php

declare(strict_types=1);

namespace App\Exception\DownloadedFileLog;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadedFileNotFoundException extends NotFoundHttpException
{

    public static function fromDownloadedFileHash(string $hash): self
    {
        throw new self(\sprintf('Downloaded file with hash "%s" not found.', $hash));
    }

}
