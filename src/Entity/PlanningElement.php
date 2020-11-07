<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlanningElementRepository")
 * @ORM\Table(name="amap_planning_element")
 */
class PlanningElement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Planning", inversedBy="elements")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $planning;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="planningElements")
     * @ORM\JoinTable(name="amap_planning_element_user")
     * @ORM\OrderBy({"lastname" = "ASC"})
     */
    private $members;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     */
    private $date;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlanning(): ?Planning
    {
        return $this->planning;
    }

    public function getMembers(): ?Collection
    {
        return $this->members;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setPlanning(Planning $planning): self
    {
        $this->planning = $planning;

        return $this;
    }

    public function setMembers($members): self
    {
        $this->members->clear();

        foreach ($members as $member) {
            $this->members->add($member);
        }

        return $this;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getId();
    }
}
