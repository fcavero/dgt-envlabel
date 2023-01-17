<?php

declare(strict_types=1);


namespace App\Service\Label;

use App\Entity\Label;
use App\Repository\LabelRepository;

class GetEnvLabelService
{
    private LabelRepository $labelRepository;

    public function __construct(LabelRepository $labelRepository)
    {
        $this->labelRepository = $labelRepository;
    }

    public function findEnvLabelById(int $id): Label
    {
        return $this->labelRepository->findByIdOrFail($id);
    }

}
