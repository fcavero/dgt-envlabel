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
    private FilesystemService $filesystemService;

    private LoggerInterface $logger;


    public function __construct(FilesystemService $filesystemService, LoggerInterface $logger)
    {
        $this->filesystemService = $filesystemService;
        $this->logger = $logger;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function download(string $dgtEnvLabelsUrl, string $zipStorageDir, string $zipFilename): string
    {
        $this->filesystemService->createDir($zipStorageDir);

        $client = new CurlHttpClient();
        $url = $dgtEnvLabelsUrl . $zipFilename;
        $zipFilepath = $zipStorageDir . $zipFilename;
        $response = $client->request('GET', $url);

        if (Response::HTTP_OK !== ($statusCode = $response->getStatusCode())) {
            throw DownloadDgtEnvLabelsFileException::fromRemoteServerRequest($url, $statusCode);
        }

        $fileHandler = fopen($zipFilepath, 'wb');
        foreach ($client->stream($response) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }

        if (!$this->filesystemService->checkIfExists($zipFilepath)) {
            $this->logger->error(
                sprintf('Environmental labels file has not been downloaded from DGT [ %s ]', $url));
            throw FilesystemException::fileDoesNotExist($zipFilepath);
        }

        $this->logger->info(
            sprintf('Environmental labels file has been downloaded successfully from DGT [ %s ]', $zipFilepath));

        return md5_file($zipFilepath);
    }

}
