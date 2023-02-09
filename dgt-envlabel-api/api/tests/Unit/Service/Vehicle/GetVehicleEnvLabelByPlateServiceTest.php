<?php

declare(strict_types=1);

use App\Entity\Label;
use App\Entity\Vehicle;
use App\Exception\Vehicle\VehicleNotFoundException;
use App\Repository\VehicleRepository;
use App\Service\Vehicle\GetVehicleEnvLabelByPlateService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetVehicleEnvLabelByPlateServiceTest extends TestCase
{

    protected VehicleRepository|MockObject $vehicleRepository;

    private GetVehicleEnvLabelByPlateService $service;


    public function setUp(): void
    {
        parent::setUp();

        $this->vehicleRepository = $this->getMockBuilder(VehicleRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->service = new GetVehicleEnvLabelByPlateService($this->vehicleRepository);
    }

    public function testFindVehicleEnvLabelByPlateTest_ok(): void
    {
        $licensePlate = '9999LLL';
        $label = new Label(1, '16T0', 'CERO EMISIONES');
        $mockedVehicle = new Vehicle($licensePlate, $label);

        $this->vehicleRepository
            ->expects($this->once())
            ->method('findLatestEnvLabelByPlateOrFail')
            ->with($licensePlate)
            ->willReturn($mockedVehicle);

        $vehicle = $this->service->findVehicleLatestEnvLabelByPlate($licensePlate);

        $this->assertEquals($mockedVehicle->getId(), $vehicle->getId());
        $this->assertEquals($mockedVehicle->getLabel(), $vehicle->getLabel());
    }

    public function testFindVehicleEnvLabelByPlateTest_ko(): void
    {
        $fakeLicensePlate = '9999XXX';
        $exceptionMessage = \sprintf('Vehicle with license plate "%s" not found.', $fakeLicensePlate);

        $this->vehicleRepository
            ->expects($this->once())
            ->method('findLatestEnvLabelByPlateOrFail')
            ->with($fakeLicensePlate)
            ->willThrowException(new VehicleNotFoundException($exceptionMessage));

        $this->expectException(VehicleNotFoundException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->service->findVehicleLatestEnvLabelByPlate($fakeLicensePlate);
    }

    public function testFindAllVehicleEnvLabelByPlateTest_ok(): void
    {
        $licensePlate = '9999FFF';
        $labelEco = new Label(2, '16TE', 'ECO');
        $labelB = new Label(1, '16TB', 'B');
        $mockedResponse = [
            'plate' => $licensePlate,
            'labels' => [
                [
                    'createdAt' => new \DateTime(),
                    'label' => $labelEco,
                ],
                [
                    'createdAt' => new \DateTime(),
                    'label' => $labelB,
                ],
            ]
        ];

        $this->vehicleRepository
            ->expects($this->once())
            ->method('findAllEnvLabelsByPlateOrFail')
            ->with($licensePlate)
            ->willReturn($mockedResponse);

        $result = $this->service->findVehicleAllEnvLabelsByPlate($licensePlate);

        $this->assertCount(2, $result['labels']);
    }

    public function testFindAllVehicleEnvLabelByPlateTest_ko(): void
    {
        $fakeLicensePlate = '9999XXX';
        $exceptionMessage = \sprintf('Vehicle with license plate "%s" not found.', $fakeLicensePlate);

        $this->vehicleRepository
            ->expects($this->once())
            ->method('findAllEnvLabelsByPlateOrFail')
            ->with($fakeLicensePlate)
            ->willThrowException(new VehicleNotFoundException($exceptionMessage));

        $this->expectException(VehicleNotFoundException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->service->findVehicleAllEnvLabelsByPlate($fakeLicensePlate);
    }

}
