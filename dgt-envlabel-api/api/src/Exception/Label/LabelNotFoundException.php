<?php

declare(strict_types=1);

namespace App\Exception\Label;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LabelNotFoundException extends NotFoundHttpException
{
    public static function fromLabelId(int $id): self
    {
        throw new self(\sprintf('Environmental Label with ID "%s" not found.', $id));
    }

}
