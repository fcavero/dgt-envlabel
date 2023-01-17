<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class EnvLabelsFileCutterService
{
    private string $zipStorageDir;

    private int $lines;

    private string $csvStorageDir;

    private string $csvFilename;

    private FilesystemService $filesystemService;

    private LoggerInterface $logger;


    public function __construct(
        string $zipStorageDir,
        int $lines,
        string $csvStorageDir,
        string $csvFilename,
        FilesystemService $filesystemService,
        LoggerInterface $logger
    )
    {
        $this->zipStorageDir = $zipStorageDir;
        $this->lines = $lines;
        $this->csvStorageDir = $csvStorageDir;
        $this->csvFilename = $csvFilename;
        $this->filesystemService = $filesystemService;
        $this->logger = $logger;
    }

    public function split(): bool
    {
        $this->filesystemService->createDir($this->csvStorageDir);
        $csvFilepath = $this->zipStorageDir . $this->csvFilename;
        $command = [
            'split',
            sprintf('-l %s', $this->lines), // -l arg. stands for splits based on the number of lines
            $csvFilepath,
            sprintf('%s%s-', $this->csvStorageDir, $this->csvFilename), // destination
        ];

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->logger->info(
            sprintf('Environmental labels file has been chopped [ %s → %s ]', $csvFilepath, $this->csvStorageDir));

        return true;
    }

}
