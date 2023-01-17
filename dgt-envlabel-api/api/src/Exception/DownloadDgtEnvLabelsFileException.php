<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class DownloadDgtEnvLabelsFileException extends BadRequestException
{
    public static function fromRemoteServerRequest(string $url, int $statusCode): self
    {
        throw new self(\sprintf('DGT server request went wrong [ %s → %s ]', $url, $statusCode));
    }

}
