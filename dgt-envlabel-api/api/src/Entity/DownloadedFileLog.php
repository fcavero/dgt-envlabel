<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Table(name="envlabel.t_downloaded_file_log")
 * @ORM\Entity(repositoryClass="App\Repository\DownloadedFileLogRepository")
 */
class DownloadedFileLog
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid", name="id")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private string $id;

    /**
     * @ORM\Column(type="datetime", name="tms_creation")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="string", name="file_hash")
     */
    private string $fileHash;


    public function __construct(string $fileHash)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTime();
        $this->fileHash = $fileHash;
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getFileHash(): string
    {
        return $this->fileHash;
    }

}
