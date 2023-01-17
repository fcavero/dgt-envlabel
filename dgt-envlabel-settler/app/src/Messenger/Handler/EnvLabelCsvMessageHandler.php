<?php

declare(strict_types=1);

namespace Settler\Messenger\Handler;

use Doctrine\DBAL\Exception;
use Settler\Messenger\Message\EnvLabelCsvMessage;
use Settler\Service\CsvFileBulkInsertService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EnvLabelCsvMessageHandler implements MessageHandlerInterface
{
    private CsvFileBulkInsertService $bulkInsertService;


    public function __construct(CsvFileBulkInsertService $bulkInsertService)
    {
        $this->bulkInsertService = $bulkInsertService;
    }

    /**
     * @throws Exception
     */
    public function __invoke(EnvLabelCsvMessage $message): void
    {
        $this->bulkInsertService->loadAndInsertLineByLine($message->getFile());
    }

}
