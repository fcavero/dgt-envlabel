<?php

declare(strict_types=1);

namespace App\Messenger\Message;

class EnvLabelCsvMessage
{
    private string $file;


    public function __construct(string $file)
    {
        $this->file = $file;
    }


    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

}
