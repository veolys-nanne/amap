<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AvailabilityScheduleElementRepository")
 * @ORM\Table(name="amap_availability_schedule_element")
 */
class AvailabilityScheduleElement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AvailabilitySchedule", inversedBy="elements")
     * @Assert\NotBlank()
     */
    private $availabilitySchedule;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAvailable = false;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThanOrEqual("today")
     * @Assert\NotBlank()
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function getAvailabilitySchedule(): ?AvailabilitySchedule
    {
        return $this->availabilitySchedule;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function setAvailabilitySchedule(AvailabilitySchedule $availabilitySchedule): self
    {
        $this->availabilitySchedule = $availabilitySchedule;

        return $this;
    }
}
