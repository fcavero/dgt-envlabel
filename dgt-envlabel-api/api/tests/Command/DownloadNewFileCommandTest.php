<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

class DownloadNewFileCommandTest extends KernelTestCase
{
    use RecreateDatabaseTrait;

    public function testDownloadDgtEnvLabelsFileNewFile_ok(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:download-environmental-labels-file');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.amqp_csv');
        $this->assertGreaterThanOrEqual(1, $transport->get());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('let\'s unzip & split the downloaded file!', $output);
        $this->assertStringContainsString('let\'s send those messages to RabbitMQ!', $output);
        $this->assertStringContainsString('messages (CSV filenames) have been sent.', $output);
        $this->assertStringContainsString('Downloaded file hash has been logged!', $output);
        $this->assertStringContainsString('Downloaded zip file has been deleted successfully.', $output);
        $this->assertStringContainsString('app:download-environmental-labels-file → Finished', $output);

        $this->cleanSplitFilesIfNecessary();
    }

    private function cleanSplitFilesIfNecessary(): void
    {
        $filesystem = new Filesystem();
        $finder = new Finder();

        $finder->files()->in($_ENV['SPLIT_COMMAND_STORAGE_DIR'])
            ->name([sprintf('%s-*', $_ENV['DGT_ENVIRONMENTAL_LABELS_CSV_FILE'])]);

        foreach ($finder as $file) {
            if ($filesystem->exists($file->getRealPath())) {
                $filesystem->remove($file);
            }
        }
    }

}
