<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\EnvLabelsFileCutterService;
use App\Service\EnvLabelsFileUnzipperService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessEnvLabelsCommand extends Command
{
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
            ->setHelp("Processes the huge file of environmental labels.")
            ->setDescription('Unzips the downloaded file of environmental labels, and splits the vast CSV '.
                'file, basing this split process on a defined number of lines per file, and it\'s been done using a '.
                'OS command called «split».');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([\sprintf('%s %s → Starting...', date('Y-m-d H:i:s'), $this->getName()),]);

        if (!$this->unzipDownloadedDgtEnvLabelsFileService->unzip()) {
            $output->writeln([
                ' → Something weird has happened unzipping the file. Please, check out process logs to figure it out.'
            ]);
            $this->printFinishMessage($output);
            return Command::FAILURE;
        }
        $output->writeln([' → The file has been unzipped successfully.', '',]);

        $output->writeln([
            \sprintf('%s %s → Splitting CSV file...', $this->getTimestamp(), $this->getName()),
        ]);

        if (!$this->envLabelsCutterService->split()) {
            $output->writeln([
                ' → Something weird has happened splitting the file. Please, check out process logs to figure it out.'
            ]);
            $this->printFinishMessage($output);
            return Command::FAILURE;
        }
        $output->writeln([' → The file has been chopped successfully.',]);

        $this->printFinishMessage($output);
        return Command::SUCCESS;
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
