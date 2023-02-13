<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\DownloadedFileLog;
use App\Repository\DownloadedFileLogRepository;

class DownloadedFileLogService
{

    private DownloadedFileLogRepository $downloadedFileLogRepository;

    public function __construct(DownloadedFileLogRepository $downloadedFileLogRepository)
    {
        $this->downloadedFileLogRepository = $downloadedFileLogRepository;
    }


    public function checkFileHash(string $hash): bool
    {
        $file = $this->downloadedFileLogRepository->findLatestDownloadedFileOrNull();
        if (null === $file) {
            return true; // The very first file downloaded. Impressive.
        }

        return ($hash !== $file->getFileHash());
    }

    public function logFileHash(string $hash): void
    {
        $file = new DownloadedFileLog($hash);
        $this->downloadedFileLogRepository->save($file, true);
    }

}
