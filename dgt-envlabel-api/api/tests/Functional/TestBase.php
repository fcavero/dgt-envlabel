<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TestBase extends WebTestCase
{
    use RecreateDatabaseTrait;

    protected AbstractDatabaseTool $databaseTool;

    protected static ?KernelBrowser $client = null;
    protected static ?KernelBrowser $labelClient = null;
    protected static ?KernelBrowser $vehicleClient = null;

    protected string $labelsEndpoint;

    protected string $vehiclesEndpoint;


    protected function setUp(): void
    {

        if (null === self::$client) {
            self::$client = static::createClient();
        }

        if (null === self::$labelClient) {
            self::$labelClient = clone self::$client;
            $this->prepareClient(self::$labelClient);
        }

        if (null === self::$vehicleClient) {
            self::$vehicleClient = clone self::$client;
            $this->prepareClient(self::$vehicleClient);
        }


        $this->labelsEndpoint = '/v1/labels';
        $this->vehiclesEndpoint = '/v1/vehicles';
    }

    private function prepareClient(KernelBrowser $client): void
    {
        $client->setServerParameters(
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $this->databaseTool = $client->getContainer()->get(DatabaseToolCollection::class)->get();
    }

    protected function getLabelByIdRequestURI(int $id): string
    {
        return \sprintf('%s/%s', $this->labelsEndpoint, $id);
    }

    protected function getLabelByDescriptionRequestURI(string $description): string
    {
        return \sprintf('%s/common_name/%s', $this->labelsEndpoint, $description);
    }

    protected function getVehicleRequestURI(string $plate, bool $historic = false): string
    {
        return ($historic)
            ? \sprintf('%s/all/%s', $this->vehiclesEndpoint, $plate)
            : \sprintf('%s/latest/%s', $this->vehiclesEndpoint, $plate);
    }

    /**
     * @throws \JsonException
     */
    protected function getResponseData(Response $response): array
    {
        return \json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function initDbConnection(): Connection
    {
        return self::getContainer()->get('doctrine')->getConnection();
    }

    /**
     * @throws Exception
     */
    protected function getLabelIdByTag(string $tag): mixed
    {
        return $this->initDbConnection()
            ->executeQuery(sprintf('SELECT id FROM envlabel__tt_envlabel WHERE txt_dgt_tag = \'%s\'', $tag))
            ->fetchOne();
    }

}
