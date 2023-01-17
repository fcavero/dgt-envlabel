<?php

declare(strict_types=1);


namespace App\Api\Action;

use App\Entity\Label;
use App\Service\Label\GetEnvLabelService;

class GetEnvLabel
{
    private GetEnvLabelService $getEnvLabelService;

    public function __construct(GetEnvLabelService $getEnvLabelService)
    {
        $this->getEnvLabelService = $getEnvLabelService;
    }

    public function __invoke(int $id): Label
    {
        return $this->getEnvLabelService->findEnvLabelById($id);
    }

}
