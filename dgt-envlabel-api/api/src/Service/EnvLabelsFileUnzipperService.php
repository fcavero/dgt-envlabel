<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\FilesystemException;
use Psr\Log\LoggerInterface;
use ZipArchive;
use function sprintf;

class EnvLabelsFileUnzipperService
{
    private FilesystemService $filesystemService;

    private LoggerInterface $logger;


    public function __construct(
        FilesystemService $filesystemService,
        LoggerInterface $logger
    )
    {
        $this->filesystemService = $filesystemService;
        $this->logger = $logger;
    }

    public function unzip(string $zipFilepath): bool
    {
        $extractPath = pathinfo(realpath($zipFilepath), PATHINFO_DIRNAME);
        $zip = new ZipArchive;
        if (true === ($res = $zip->open($zipFilepath, ZipArchive::CHECKCONS))) {
            $zip->extractTo($extractPath);
            $zip->close();

            if (!$this->filesystemService->checkIfExists($zipFilepath)) {
                $this->logger->error(
                    sprintf('Environmental labels file has not been unzipped [ %s ]', $zipFilepath));
                throw FilesystemException::fileDoesNotExist($zipFilepath);
            }

            $this->logger->info(
                sprintf('Environmental labels file has been unzipped successfully [ %s ]', $zipFilepath));

            return true;
        }

        $message = match ($res) {
            ZipArchive::ER_NOZIP => sprintf('%s is not a ZIP file! (%s)', $zipFilepath, ZipArchive::ER_NOZIP),
            ZipArchive::ER_INCONS => sprintf('%s consistency check failed! (%s)', $zipFilepath, ZipArchive::ER_INCONS),
            ZipArchive::ER_CRC => sprintf('%s checksum failed! (%s)', $zipFilepath, ZipArchive::ER_CRC),
            default => sprintf('Unable to open %s file!', $zipFilepath),
        };

        $this->logger->error($message);
        FilesystemException::fromUnzipProcess($message);
    }

}
