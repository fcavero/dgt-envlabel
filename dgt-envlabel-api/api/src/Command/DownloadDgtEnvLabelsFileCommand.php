<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\DownloadDgtEnvLabelsFileService;
use App\Service\DownloadedFileLogService;
use App\Service\FilesystemService;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class DownloadDgtEnvLabelsFileCommand extends Command
{
    private string $zipStorageDir;

    private string $zipFilename;

    private string $dgtEnvLabelsUrl;

    private DownloadDgtEnvLabelsFileService $downloadDgtEnvLabelsFileService;

    private DownloadedFileLogService $downloadedFileLogService;

    private FilesystemService $filesystemService;


    public function __construct(
        string $zipStorageDir,
        string $zipFilename,
        string $dgtEnvLabelsUrl,
        DownloadDgtEnvLabelsFileService $downloadDgtEnvLabelsFileService,
        DownloadedFileLogService $downloadedFileLogService,
        FilesystemService $filesystemService
    )
    {
        parent::__construct();
        $this->zipStorageDir = $zipStorageDir;
        $this->zipFilename = $zipFilename;
        $this->dgtEnvLabelsUrl = $dgtEnvLabelsUrl;
        $this->downloadDgtEnvLabelsFileService = $downloadDgtEnvLabelsFileService;
        $this->downloadedFileLogService = $downloadedFileLogService;
        $this->filesystemService = $filesystemService;
    }

    protected function configure() : void
    {
        $this
            ->setName('app:download-environmental-labels-file')
            ->setAliases(['app:download-file'])
            ->setDescription("Gets the complete official file of vehicles' environmental labels.")
            ->setHelp('Downloads a compressed file from DGT (Dirección General de Tráfico) '.
                'servers containing the environmental labels of the whole vehicle fleet in Spain. After that, gets '.
                'its hash and checks if it is different of the previous downloaded one; if yes, triggers its process.');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([\sprintf('%s %s → Starting...', date('Y-m-d H:i:s'), $this->getName()),]);

        $zipFilepath = $this->zipStorageDir . $this->zipFilename;
        if (!($hash = $this->downloadDgtEnvLabelsFileService
                ->download($this->dgtEnvLabelsUrl, $this->zipStorageDir, $this->zipFilename))) {
            $output->writeln([
                ' → Something weird has happened downloading the file. Please, check out process logs to figure it out.'
            ]);
            $this->printFinishMessage($output);
            return Command::FAILURE;
        }

        $output->writeln([' → DGT env. labels zip file has been downloaded successfully.', '',]);

        if ($this->postDownloadActions($zipFilepath, $hash, $output)) {
            $this->printFinishMessage($output);
            return Command::SUCCESS;
        }

        $this->printFinishMessage($output);
        $this->purgeZipFile($zipFilepath, $output);

        return  Command::FAILURE;
    }

    /**
     * @throws ExceptionInterface
     * @throws Exception
     */
    private function postDownloadActions(string $zipFilepath, string $hash, OutputInterface $output): bool
    {
        $output->writeln([
            sprintf(' → Verifying differences between the new and the previous one [ hash: %s ]...', $hash),
            '',
        ]);

        if (!$this->downloadedFileLogService->checkFileHash($hash)) {
            $output->writeln([
                ' → Downloaded file is identical to the previous one: environmental labels update process aborted!'
            ]);

            return $this->purgeZipFile($zipFilepath, $output);
        }

        // Go for the zip file processing: unzipping && splitting (then sending to RabbitMQ)
        $output->writeln([" → Going further → let's unzip & split the downloaded file!", '',]);
        if (Command::SUCCESS === $this->runProcessEnvLabelsCommand($zipFilepath, $output)) {
            $this->downloadedFileLogService->logFileHash($hash);

            return $this->purgeZipFile($zipFilepath, $output);
        }
        return false;
    }

    /**
     * @throws ExceptionInterface
     */
    private function runProcessEnvLabelsCommand(string $zipFilePath, OutputInterface $output): int
    {
        if (null !== ($command = $this->getApplication()->find('app:process-environmental-label-file'))) {
            $commandArrayInput = new ArrayInput(['zipFilepath' => $zipFilePath,]);
            return $command->run($commandArrayInput, $output);
        }
        return Command::INVALID;
    }

    private function purgeZipFile(string $zipFilePath, OutputInterface $output): bool
    {
        if ($this->filesystemService->removeSingleFile($zipFilePath)) {
            $output->writeln([' → Downloaded zip file has been deleted successfully.']);
            return true;
        }
        $output->writeln([
            ' → Something weird has happened removing the zip file. Please, check out process logs to figure it out.'
        ]);

        return false;
    }

    private function printFinishMessage(OutputInterface $output): void
    {
        $output->writeln(['',
            \sprintf('%s %s → Finished', date('Y-m-d H:i:s'), $this->getName()), '',
        ]);
    }

}
