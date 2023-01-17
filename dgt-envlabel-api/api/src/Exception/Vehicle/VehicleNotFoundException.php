<?php

declare(strict_types=1);

namespace App\Exception\Vehicle;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VehicleNotFoundException extends NotFoundHttpException
{
    public static function fromVehiclePlate(string $id): self
    {
        throw new self(\sprintf('Vehicle with license plate "%s" not found.', $id));
    }

}
