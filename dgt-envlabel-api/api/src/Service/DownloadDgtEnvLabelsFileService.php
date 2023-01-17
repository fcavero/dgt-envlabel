<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\DownloadDgtEnvLabelsFileException;
use App\Exception\FilesystemException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use function sprintf;

class DownloadDgtEnvLabelsFileService
{
    private string $zipStorageDir;
    private string $zipFilename;
    private string $dgtEnvLabelsUrl;

    private FilesystemService $filesystemService;

    private LoggerInterface $logger;


    public function __construct(
        string $zipStorageDir,
        string $zipFilename,
        string $dgtEnvLabelsUrl,
        FilesystemService $filesystemService,
        LoggerInterface $logger
    )
    {
        $this->zipStorageDir = $zipStorageDir;
        $this->zipFilename = $zipFilename;
        $this->dgtEnvLabelsUrl = $dgtEnvLabelsUrl;
        $this->filesystemService = $filesystemService;
        $this->logger = $logger;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function download(): string
    {
        $this->filesystemService->createDir($this->zipStorageDir);

        $client = new CurlHttpClient();
        $url = $this->dgtEnvLabelsUrl . $this->zipFilename;
        $filepath = $this->zipStorageDir . $this->zipFilename;
        $response = $client->request('GET', $url);

        if (Response::HTTP_OK !== ($statusCode = $response->getStatusCode())) {
            throw DownloadDgtEnvLabelsFileException::fromRemoteServerRequest($url, $statusCode);
        }

        $fileHandler = fopen($filepath, 'wb');
        foreach ($client->stream($response) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }

        if (!$this->filesystemService->checkIfExists($filepath)) {
            $this->logger->error(
                sprintf('Environmental labels file has not been downloaded from DGT [ %s ]', $url));
            throw FilesystemException::fileDoesNotExist($filepath);
        }

        $this->logger->info(
            sprintf('Environmental labels file has been downloaded successfully from DGT [ %s ]', $filepath));

        return $filepath;
    }

}
