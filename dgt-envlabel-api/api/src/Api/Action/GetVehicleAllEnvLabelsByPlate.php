<?php

declare(strict_types=1);

namespace App\Api\Action;

use App\Entity\Vehicle;
use App\Service\Vehicle\GetVehicleEnvLabelByPlateService;

class GetVehicleAllEnvLabelsByPlate
{
    private GetVehicleEnvLabelByPlateService $getVehicleEnvLabelByPlateService;


    public function __construct(GetVehicleEnvLabelByPlateService $getVehicleEnvLabelByPlateService)
    {
        $this->getVehicleEnvLabelByPlateService = $getVehicleEnvLabelByPlateService;
    }

    public function __invoke(string $plate): Vehicle|array
    {
        /** @var Vehicle[] $vehicles */
        $vehicles = $this->getVehicleEnvLabelByPlateService->findVehicleAllEnvLabelsByPlate($plate);

        if (count($vehicles) === 1) {
            return $vehicles[0];
        }

        $response = [
            'plate'  => $vehicles[0]->getPlate(),
            'labels' => [],
        ];
        foreach ($vehicles as $veh) {
            $response['labels'][] = [
                'createdAt' => $veh->getCreatedAt(),
                'label'     => $veh->getLabel(),
            ];
        }
        return $response;
    }
}
