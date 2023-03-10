<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\FilesystemException;
use App\Messenger\Message\EnvLabelCsvMessage;
use App\Messenger\RoutingKey;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

class SendEnvLabelCsvMessagesService
{
    private string $csvStorageDir;

    private MessageBusInterface $messageBus;

    private LoggerInterface $logger;


    public function __construct(
        string $csvStorageDir,
        MessageBusInterface $messageBus,
        LoggerInterface $logger
    )
    {
        $this->csvStorageDir = $csvStorageDir;
        $this->messageBus = $messageBus;
        $this->logger = $logger;
    }

    public function scanAndSend(): int
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
        }
        $this->logger->info('All messages have been sent to RabbitMQ successfully.');

        return $messages;
    }

}
