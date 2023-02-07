<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Table(name="envlabel.t_vehicle")
 * @ORM\Entity(repositoryClass="App\Repository\VehicleRepository")
 */
class Vehicle
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid", name="id")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private string $id;

    /**
     * @ORM\Column(type="string", name="txt_plate")
     */
    private string $plate;

    /**
     * @ORM\Column(type="datetime", name="tms_creation")
     */
    private DateTime $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Label")
     * @ORM\JoinColumn(name="envlabel_id", referencedColumnName="id")
     */
    private Label $label;


    public function __construct(string $plate, Label $label, DateTime $createdAt = null)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->plate = $plate;
        $this->createdAt = ($createdAt == null) ? new \DateTime() : $createdAt;
        $this->label = $label;
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getPlate(): string
    {
        return $this->plate;
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
