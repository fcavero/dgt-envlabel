<?php

declare(strict_types=1);

namespace App\Api\Action\Vehicle;

use App\Entity\Vehicle;
use App\Service\Vehicle\GetVehicleEnvLabelByPlateService;

class GetVehicleLatestEnvLabelByPlate
{
    private GetVehicleEnvLabelByPlateService $getVehicleEnvLabelByPlateService;


    public function __construct(GetVehicleEnvLabelByPlateService $getVehicleEnvLabelByPlateService)
    {
        $this->getVehicleEnvLabelByPlateService = $getVehicleEnvLabelByPlateService;
    }

    public function __invoke(string $plate): Vehicle
    {
        return $this->getVehicleEnvLabelByPlateService->findVehicleLatestEnvLabelByPlate($plate);
    }

}
