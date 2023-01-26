<?php

declare(strict_types=1);

namespace App\Service\Vehicle;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;

class GetVehicleEnvLabelByPlateService
{
    private VehicleRepository $vehicleRepository;


    public function __construct(VehicleRepository $vehicleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    public function findVehicleLatestEnvLabelByPlate(string $plate): Vehicle
    {
        return $this->vehicleRepository->findLatestEnvLabelByPlateOrFail($plate);
    }

    public function findVehicleAllEnvLabelsByPlate(string $plate): array
    {
        return $this->vehicleRepository->findAllEnvLabelsByPlateOrFail($plate);
    }
}
