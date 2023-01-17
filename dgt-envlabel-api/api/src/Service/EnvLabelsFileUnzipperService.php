<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\FilesystemException;
use Psr\Log\LoggerInterface;
use ZipArchive;
use function sprintf;

class EnvLabelsFileUnzipperService
{
    private string $zipStorageDir;
    private string $zipFilename;

    private FilesystemService $filesystemService;

    private LoggerInterface $logger;


    public function __construct(
        string $zipStorageDir,
        string $zipFilename,
        FilesystemService $filesystemService,
        LoggerInterface $logger
    )
    {
        $this->zipStorageDir = $zipStorageDir;
        $this->zipFilename = $zipFilename;
        $this->filesystemService = $filesystemService;
        $this->logger = $logger;
    }

    public function unzip(): bool
    {
        $filepath = $this->zipStorageDir . $this->zipFilename;
        $extractPath = pathinfo(realpath($filepath), PATHINFO_DIRNAME);
        $zip = new ZipArchive;
        if (true === ($res = $zip->open($filepath, ZipArchive::CHECKCONS))) {
            $zip->extractTo($extractPath);
            $zip->close();

            if (!$this->filesystemService->checkIfExists($filepath)) {
                $this->logger->error(
                    sprintf('Environmental labels file has not been unzipped [ %s ]', $filepath));
                throw FilesystemException::fileDoesNotExist($filepath);
            }

            $this->logger->info(
                sprintf('Environmental labels file has been unzipped successfully [ %s ]', $filepath));

            return true;
        }

        $message = match ($res) {
            ZipArchive::ER_NOZIP => sprintf('«%s» is not a ZIP file! (%s)', $filepath, ZipArchive::ER_NOZIP),
            ZipArchive::ER_INCONS => sprintf('«%s» consistency check failed! (%s)', $filepath, ZipArchive::ER_INCONS),
            ZipArchive::ER_CRC => sprintf('«%s» checksum failed! (%s)', $filepath, ZipArchive::ER_CRC),
            default => sprintf('Unable to open «%s» file!', $filepath),
        };
        $this->logger->error($message);
        FilesystemException::fromUnzipProcess($message);
    }

}
