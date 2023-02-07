<?php

declare(strict_types=1);

namespace App\Exception\Label;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LabelNotFoundException extends NotFoundHttpException
{
    public static function fromLabelId(string $id): self
    {
        throw new self(\sprintf('Environmental label with ID "%s" not found.', $id));
    }

    public static function fromLabelDescription(string $description): self
    {
        throw new self(\sprintf('Environmental label with description "%s" not found.', $description));
    }

}
