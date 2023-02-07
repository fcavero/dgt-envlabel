<?php

declare(strict_types=1);

namespace App\Api\Action\Label;

use App\Entity\Label;
use App\Service\Label\GetLabelByDescriptionService;

class GetLabelByDescription
{
    private GetLabelByDescriptionService $getLabelByDescriptionService;


    public function __construct(GetLabelByDescriptionService $getLabelByDescriptionService)
    {
        $this->getLabelByDescriptionService = $getLabelByDescriptionService;
    }

    public function __invoke(string $description): Label
    {
        return $this->getLabelByDescriptionService->findLabelByDescription($description);
    }

}
