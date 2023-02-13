<?php

declare(strict_types=1);

namespace App\Api\Action\DownloadedFileLog;

use App\Entity\DownloadedFileLog;
use App\Service\DownloadedFileLog\GetDownloadedFileByHashService;

class GetDownloadedFileByHash
{

    private GetDownloadedFileByHashService $getDownloadedFileByHashService;


    public function __construct(GetDownloadedFileByHashService $getDownloadedFileByHashService)
    {
        $this->getDownloadedFileByHashService = $getDownloadedFileByHashService;
    }

    public function __invoke(string $hash): DownloadedFileLog
    {
        return $this->getDownloadedFileByHashService->findDownloadedFileByHash($hash);
    }

}
