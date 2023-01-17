<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="envlabel.t_vehicle")
 * @ORM\Entity(repositoryClass="App\Repository\VehicleRepository")
 */
class Vehicle
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", name="id")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private string $id;

    /**
     * @ORM\Column(type="datetime", name="tms_creation")
     */
    private DateTime $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Label")
     * @ORM\JoinColumn(name="envlabel_id", referencedColumnName="id")
     */
    private Label $label;

    /**
     * @param string $id
     * @param Label $label
     */
    public function __construct(string $id, Label $label)
    {
        $this->id = $id;
        $this->createdAt = new \DateTime();
        $this->label = $label;
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getLabel(): Label
    {
        return $this->label;
    }

}
