<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\FilesystemException;
use App\Messenger\Message\EnvLabelCsvMessage;
use App\Messenger\RoutingKey;
use Faker\Core\File;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

class SendEnvLabelCsvMessagesService
{
    private string $csvStorageDir;

    private FilesystemService $filesystemService;

    private MessageBusInterface $messageBus;

    private LoggerInterface $logger;


    public function __construct(
        string $csvStorageDir,
        FilesystemService $filesystemService,
        MessageBusInterface $messageBus,
        LoggerInterface $logger
    )
    {
        $this->csvStorageDir = $csvStorageDir;
        $this->filesystemService = $filesystemService;
        $this->messageBus = $messageBus;
        $this->logger = $logger;
    }

    public function scanAndSend(int $delayBetweenDeliveries = 2): int
    {
        $finder = new Finder();

        $finder->files()->in($this->csvStorageDir);

        if (!$finder->hasResults()) {
            $this->logger->error(
                sprintf('%s directory is empty!', $this->csvStorageDir));
            throw FilesystemException::emptyDirectory($this->csvStorageDir);
        }

        $messages = 0;
        foreach ($finder as $file) {
            $filepath = $file->getRealPath();
            $this->messageBus->dispatch(
                new EnvLabelCsvMessage($filepath),
                [new AmqpStamp(RoutingKey::CSV_QUEUE)]
            );
            $this->logger->debug(sprintf('File sent to RabbitMQ [ %s ]', $file->getFilename()));
            $messages++;
            sleep($delayBetweenDeliveries);
        }
        $this->logger->info('All messages have been sent to RabbitMQ successfully.');

        foreach ($finder as $file) {
            $this->filesystemService->removeSingleFile($file->getRealPath());
        }
        $this->logger->info('All CSV files have been sent to /dev/null successfully.');

        return $messages;
    }

}
