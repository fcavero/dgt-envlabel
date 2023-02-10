<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DownloadDgtEnvLabelsFileCommandTest extends KernelTestCase
{

    public function testDownloadDgtEnvLabelsFileNewFile_ok(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:download-environmental-labels-file');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('let\'s unzip & split the downloaded file!', $output);
        $this->assertStringContainsString('let\'s send those messages to RabbitMQ!', $output);
        $this->assertStringContainsString('messages (CSV filenames) have been sent.', $output);
        $this->assertStringContainsString('Downloaded file hash has been logged!', $output);
        $this->assertStringContainsString('Downloaded zip file has been deleted successfully.', $output);
        $this->assertStringContainsString('app:download-environmental-labels-file → Finished', $output);
    }

    public function testDownloadDgtEnvLabelsFileAlreadyProcessedFile_ok(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:download-environmental-labels-file');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Downloaded file is identical to the previous one', $output);
        $this->assertStringContainsString('Downloaded zip file has been deleted successfully.', $output);
        $this->assertStringContainsString('app:download-environmental-labels-file → Finished', $output);
    }

}
