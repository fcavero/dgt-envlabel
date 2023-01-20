<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\SendEnvLabelCsvMessagesService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendEnvLabelCsvMessagesCommand extends Command
{
    private SendEnvLabelCsvMessagesService $sendEnvLabelCsvMessagesService;


    public function __construct(SendEnvLabelCsvMessagesService $sendEnvLabelCsvMessagesService)
    {
        parent::__construct();
        $this->sendEnvLabelCsvMessagesService = $sendEnvLabelCsvMessagesService;
    }

    protected function configure() : void
    {
        $this
            ->setName('app:send-environmental-labels-csv-messages')
            ->setAliases(['app:send-csv-msg'])
            ->addArgument('delayBetweenDeliveries', InputArgument::OPTIONAL, 'Waiting seconds between CSV messages.')
            ->setDescription("Sends one message per each CSV chopped file to RabbitMQ.")
            ->setHelp('Loops through the CSV file directory sending one message per each one of '.
                'them to RabbitMQ, where a consumer will get them one by one asynchronous and will perform '.
                'the bulk insert.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([\sprintf('%s %s → Starting...', date('Y-m-d H:i:s'), $this->getName()),]);

        $delay = $input->getArgument('delayBetweenDeliveries');
        $messages = $this->sendEnvLabelCsvMessagesService->scanAndSend($delay);

        $output->writeln([
            sprintf(' → %s messages ─one per each CSV file─ have been sent.', $messages), '',
        ]);

        return Command::SUCCESS;
    }

}
