<?php

declare(strict_types=1);

namespace App\Service\Vehicle;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;

class GetVehicleEnvLabelService
{
    private VehicleRepository $vehicleRepository;

    public function __construct(VehicleRepository $vehicleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    public function findVehicleEnvLabelByPlate(string $id): Vehicle
    {
        return $this->vehicleRepository->findByIdOrFail($id);
    }

}
