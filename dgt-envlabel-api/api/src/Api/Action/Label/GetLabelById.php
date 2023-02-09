<?php

declare(strict_types=1);

namespace App\Api\Action\Label;

use App\Entity\Label;
use App\Service\Label\GetLabelByIdService;

class GetLabelById
{
    private GetLabelByIdService $getLabelByIdService;


    public function __construct(GetLabelByIdService $getLabelByIdService)
    {
        $this->getLabelByIdService = $getLabelByIdService;
    }

    public function __invoke(int $id): Label
    {
        return $this->getLabelByIdService->findLabelById($id);
    }

}
