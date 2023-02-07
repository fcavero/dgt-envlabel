<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class VehicleTest extends TestBase
{

    /**
     * @throws Exception
     */
    public function testGetVehicleAllEnvLabelsByPlate_ok(): void
    {
        $plate = '0001DDD';

        self::$vehicleClient->request('GET', $this->getVehicleRequestURI($plate, true));

        $response = self::$vehicleClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($plate, $responseData['plate']);
        $this->assertCount(2, $responseData['labels']);
    }

    /**
     * @throws Exception
     */
    public function testGetSinDistintivoVehicleLatestEnvLabelsByPlate_ok(): void
    {
        $plate = '0000BBB';

        self::$vehicleClient->request('GET', $this->getVehicleRequestURI($plate));

        $response = self::$vehicleClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($plate, $responseData['plate']);
        $this->assertEquals('SIN DISTINTIVO', $responseData['label']['tag']);
    }

    /**
     * @throws Exception
     */
    public function testGetBVehicleLatestEnvLabelsByPlate_ok(): void
    {
        $plate = '0002DDD';

        self::$vehicleClient->request('GET', $this->getVehicleRequestURI($plate));

        $response = self::$vehicleClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($plate, $responseData['plate']);
        $this->assertEquals('B', $responseData['label']['description']);
    }

    /**
     * @throws Exception
     */
    public function testGetCVehicleLatestEnvLabelsByPlate_ok(): void
    {
        $plate = '0002FFF';

        self::$vehicleClient->request('GET', $this->getVehicleRequestURI($plate));

        $response = self::$vehicleClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($plate, $responseData['plate']);
        $this->assertEquals('C', $responseData['label']['description']);
    }

    /**
     * @throws Exception
     */
    public function testGetEcoVehicleLatestEnvLabelsByPlate_ok(): void
    {
        $plate = '0005JJJ';

        self::$vehicleClient->request('GET', $this->getVehicleRequestURI($plate));

        $response = self::$vehicleClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($plate, $responseData['plate']);
        $this->assertEquals('ECO', $responseData['label']['description']);
    }

    /**
     * @throws Exception
     */
    public function testGetCeroVehicleLatestEnvLabelsByPlate_ok(): void
    {
        $plate = '0007LLL';

        self::$vehicleClient->request('GET', $this->getVehicleRequestURI($plate));

        $response = self::$vehicleClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($plate, $responseData['plate']);
        $this->assertEquals('16T0', $responseData['label']['tag']);
    }

    /**
     * @throws Exception
     */
    public function testGetVehicleLatestEnvLabelsByPlate_ko(): void
    {
        $plate = '9999BBB';

        self::$vehicleClient->request('GET', $this->getVehicleRequestURI($plate));

        $response = self::$vehicleClient->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

}
