<?php

declare(strict_types=1);

namespace App\Exception\Vehicle;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VehicleNotFoundException extends NotFoundHttpException
{
    public static function fromVehicleId(string $id): self
    {
        throw new self(\sprintf('Vehicle with ID "%s" not found.', $id));
    }

    public static function fromVehiclePlate(string $plate): self
    {
        throw new self(\sprintf('Vehicle with license plate "%s" not found.', $plate));
    }

}
