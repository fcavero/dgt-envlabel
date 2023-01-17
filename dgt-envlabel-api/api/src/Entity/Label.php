<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="envlabel.tt_envlabel")
 * @ORM\Entity(repositoryClass="App\Repository\LabelRepository")
 */
class Label
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", name="txt_dgt_tag")
     */
    private string $tag;

    /**
     * @ORM\Column(type="string", name="txt_description")
     */
    private string $description;

    /**
     * @param string $tag
     * @param string $description
     */
    public function __construct(string $tag, string $description)
    {
        $this->tag = $tag;
        $this->description = $description;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

}
