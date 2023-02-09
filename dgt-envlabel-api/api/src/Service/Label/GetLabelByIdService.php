<?php

declare(strict_types=1);

namespace App\Service\Label;

use App\Entity\Label;
use App\Repository\LabelRepository;

class GetLabelByIdService
{
    private LabelRepository $labelRepository;


    public function __construct(LabelRepository $labelRepository)
    {
        $this->labelRepository = $labelRepository;
    }

    public function findLabelById(int $id): Label
    {
        return $this->labelRepository->findLabelByIdOrFail($id);
    }

}
