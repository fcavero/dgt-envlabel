<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\EnvLabelsFileCutterService;
use App\Service\EnvLabelsFileUnzipperService;
use App\Service\FilesystemService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessEnvLabelsCommand extends Command
{
    private const DELAY_BETWEEN_DELIVERIES = 1;

    private EnvLabelsFileUnzipperService $unzipDownloadedDgtEnvLabelsFileService;

    private EnvLabelsFileCutterService $envLabelsCutterService;


    public function __construct(
        EnvLabelsFileUnzipperService $unzipDownloadedDgtEnvLabelsFileService,
        EnvLabelsFileCutterService $envLabelsCutterService
    )
    {
        parent::__construct();
        $this->unzipDownloadedDgtEnvLabelsFileService = $unzipDownloadedDgtEnvLabelsFileService;
        $this->envLabelsCutterService = $envLabelsCutterService;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:process-environmental-label-file')
            ->setAliases(['app:process-file'])
            ->addArgument('zipFilepath', InputArgument::REQUIRED, 'The zip file of environmental labels.')
            ->setDescription("Processes the huge file of environmental labels.")
            ->setHelp('Unzips the downloaded file of environmental labels, and splits the vast CSV '.
                'file, basing this split process on a defined number of lines per file, and it\'s been done using a '.
                'OS command called «split».');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([\sprintf('%s %s → Starting...', $this->getTimestamp(), $this->getName()),]);

        if ($this->doProcessActions($input, $output)) {
            $output->writeln([" → Going further → let's send those messages to RabbitMQ!", '',]);
            if (Command::SUCCESS === $this->runSendEnvLabelCsvMessagesCommand($output)) {

                $this->printFinishMessage($output);
                return Command::SUCCESS;
            }
        }

        $this->printFinishMessage($output);
        return Command::FAILURE;
    }

    private function doProcessActions(InputInterface $input, OutputInterface $output): bool
    {
        if (!$this->unzipDownloadedDgtEnvLabelsFileService->unzip($input->getArgument('zipFilepath'))) {
            $output->writeln([
                ' → Something weird has happened unzipping the file. Please, check out process logs to figure it out.'
            ]);
            $this->printFinishMessage($output);

            return false;
        }
        $output->writeln([' → The file has been unzipped successfully.',
            '',
            ' → Splitting CSV file...',
        ]);
        if (!$this->envLabelsCutterService->split()) {
            $output->writeln([
                ' → Something weird has happened splitting the file. Please, check out process logs to figure it out.'
            ]);
            $this->printFinishMessage($output);

            return false;
        }
        $output->writeln([' → The file has been chopped successfully.', '',]);

        return true;
    }

    private function runSendEnvLabelCsvMessagesCommand(OutputInterface $output): int
    {
        if (null !== ($command = $this->getApplication()->find('app:send-environmental-labels-csv-messages'))) {
            $commandArrayInput = new ArrayInput([]);
            return $command->run($commandArrayInput, $output);
        }
        return Command::INVALID;
    }

    private function printFinishMessage(OutputInterface $output): void
    {
        $output->writeln(['',
            \sprintf('%s %s → Finished', $this->getTimestamp(), $this->getName()), '',
        ]);
    }

    private function getTimestamp(): string
    {
        return date('Y-m-d H:i:s');
    }
}
