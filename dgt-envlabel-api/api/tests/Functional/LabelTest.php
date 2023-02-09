<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class LabelTest extends TestBase
{
    private const SIN_DISTINTIVO = 'SIN DISTINTIVO';
    private const CERO_EMISIONES = 'CERO EMISIONES';


    public function testGetCollection(): void
    {
        self::$labelClient->request('GET', $this->labelsEndpoint);
        $response = self::$labelClient->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testGetSinDistintivoLabel_ok(): void
    {
        $id = $this->getLabelIdByTag(self::SIN_DISTINTIVO);

        self::$labelClient->request('GET', $this->getLabelByIdRequestURI($id));

        $response = self::$labelClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($id, $responseData['id']);
        $this->assertEquals(self::SIN_DISTINTIVO, $responseData['description']);
    }

    /**
     * @throws Exception
     */
    public function testGetCeroLabel_ok(): void
    {
        $id = $this->getLabelIdByTag('16T0');

        self::$labelClient->request('GET', $this->getLabelByIdRequestURI($id));

        $response = self::$labelClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($id, $responseData['id']);
        $this->assertEquals(self::CERO_EMISIONES, $responseData['description']);
    }

    /**
     * @throws Exception
     */
    public function testGetCLabel_ok(): void
    {
        $id = $this->getLabelIdByTag('16MC');

        self::$labelClient->request('GET', $this->getLabelByIdRequestURI($id));

        $response = self::$labelClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($id, $responseData['id']);
        $this->assertEquals('C', $responseData['description']);
    }

    /**
     * @throws Exception
     */
    public function testGetBLabel_ok(): void
    {
        $id = $this->getLabelIdByTag('16TB');

        self::$labelClient->request('GET', $this->getLabelByIdRequestURI($id));

        $response = self::$labelClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($id, $responseData['id']);
        $this->assertEquals('B', $responseData['description']);
    }

    /**
     * @throws Exception
     */
    public function testGetEcoLabel_ok(): void
    {
        $id = $this->getLabelIdByTag('16ME');

        self::$labelClient->request('GET', $this->getLabelByIdRequestURI($id));

        $response = self::$labelClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($id, $responseData['id']);
        $this->assertEquals('ECO', $responseData['description']);
    }

    /**
     * @throws Exception
     */
    public function testUnknownLabel_ko(): void
    {
        self::$labelClient->request('GET', $this->getLabelByDescriptionRequestURI('TETO'));

        $response = self::$labelClient->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testGetSinDistintivoLabelByDescription_ok(): void
    {
        $description = self::SIN_DISTINTIVO;

        self::$labelClient->request('GET', $this->getLabelByDescriptionRequestURI($description));

        $response = self::$labelClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($description, $responseData['description']);
    }

    /**
     * @throws Exception
     */
    public function testGetCeroLabelByDescription_ok(): void
    {
        $description = self::CERO_EMISIONES;

        self::$labelClient->request('GET', $this->getLabelByDescriptionRequestURI($description));

        $response = self::$labelClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($description, $responseData['description']);
    }

}
