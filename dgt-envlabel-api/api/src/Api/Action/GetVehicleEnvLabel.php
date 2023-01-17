<?php

declare(strict_types=1);

namespace App\Api\Action;

use App\Entity\Vehicle;
use App\Service\Vehicle\GetVehicleEnvLabelService;

class GetVehicleEnvLabel
{
    private GetVehicleEnvLabelService $getEnvironmentalLabelService;

    public function __construct(GetVehicleEnvLabelService $getEnvironmentalLabelService)
    {
        $this->getEnvironmentalLabelService = $getEnvironmentalLabelService;
    }

    public function __invoke(string $id): Vehicle
    {
        return $this->getEnvironmentalLabelService->findVehicleEnvLabelByPlate($id);
    }

}
