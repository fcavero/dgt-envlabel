<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\DownloadDgtEnvLabelsFileService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class DownloadDgtEnvLabelsFileCommand extends Command
{
    private DownloadDgtEnvLabelsFileService $downloadDgtEnvLabelsFileService;


    public function __construct(DownloadDgtEnvLabelsFileService $downloadDgtEnvLabelsFileService)
    {
        parent::__construct();
        $this->downloadDgtEnvLabelsFileService = $downloadDgtEnvLabelsFileService;
    }

    protected function configure() : void
    {
        $this
            ->setName('app:download-environmental-label-file')
            ->setHelp("Gets the official file of vehicle\'s environmental labels.")
            ->setDescription('Downloads a compressed file from DGT (Dirección General de Tráfico) '.
                'servers containing the environmental labels of the whole vehicle fleet in Spain.');
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([\sprintf('%s %s → Starting...', date('Y-m-d H:i:s'), $this->getName()),]);

        if (!$this->downloadDgtEnvLabelsFileService->download()) {
            $output->writeln([
                ' → Something weird has happened downloading the file. Please, check out process logs to figure it out.'
            ]);
            $this->printFinishMessage($output);
            return Command::FAILURE;
        }

        $output->writeln([' → DGT env. labels file has been downloaded successfully.',]);

        $this->printFinishMessage($output);

        return Command::SUCCESS;
    }

    private function printFinishMessage(OutputInterface $output): void
    {
        $output->writeln(['',
            \sprintf('%s %s → Finished', date('Y-m-d H:i:s'), $this->getName()), '',
        ]);

    }

}
