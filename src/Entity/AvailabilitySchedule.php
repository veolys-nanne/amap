<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AvailabilityScheduleRepository")
 * @ORM\Table(name="amap_availability_schedule")
 */
class AvailabilitySchedule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Planning", inversedBy="availabilitySchedules")
     * @Assert\NotBlank()
     */
    private $planning;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $member;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AvailabilityScheduleElement", mappedBy="availabilitySchedule", cascade={"all"})
     */
    private $elements;


    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlanning(): ?Planning
    {
        return $this->planning;
    }

    public function getMember(): ?User
    {
        return $this->member;
    }

    public function getElements(): ?Collection
    {
        return $this->elements;
    }

    public function setPlanning(Planning $planning): self
    {
        $this->planning = $planning;

        return $this;
    }

    public function setMember(User $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function setElements(array $elements): self
    {
        $this->elements->clear();

        foreach ($elements as $element) {
            $this->elements->add($element);
        }

        return $this;
    }

    public function addElement(AvailabilityScheduleElement $element): self
    {
        foreach ($this->elements as $currentElement) {
            if ($currentElement->getDate() == $element->getDate()) {
                $currentElement->setIsAvailable($element->getIsAvailable());

                return $this;
            }
        }

        $this->elements->add($element);

        return $this;
    }
}
