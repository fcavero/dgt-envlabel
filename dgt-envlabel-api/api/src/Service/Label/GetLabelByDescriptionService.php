<?php

declare(strict_types=1);

namespace App\Service\Label;

use App\Entity\Label;
use App\Repository\LabelRepository;

class GetLabelByDescriptionService
{
    private LabelRepository $labelRepository;


    public function __construct(LabelRepository $labelRepository)
    {
        $this->labelRepository = $labelRepository;
    }

    public function findLabelByDescription(string $description): Label
    {
        return $this->labelRepository->findLabelByDescriptionOrFail($description);
    }

}
