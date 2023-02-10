<?php

declare(strict_types=1);

namespace App\Service\DownloadedFileLog;

use App\Entity\DownloadedFileLog;
use App\Repository\DownloadedFileLogRepository;

class GetDownloadedFileByHashService
{

    private DownloadedFileLogRepository $downloadedFileLogRepository;


    public function __construct(DownloadedFileLogRepository $downloadedFileLogRepository)
    {
        $this->downloadedFileLogRepository = $downloadedFileLogRepository;
    }

    public function findDownloadedFileByHash(string $hash): DownloadedFileLog
    {
        return $this->downloadedFileLogRepository->findDownloadedFileByHashOrFail($hash);
    }

}
