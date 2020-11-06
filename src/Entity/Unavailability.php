<?php

namespace App\Entity;

use App\Doctrine\DateKey;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UnavailabilityRepository")
 * @ORM\Table(name="amap_unavailability")
 */
class Unavailability
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @Assert\NotBlank()
     */
    private $member;

    /**
     * @ORM\Id()
     * @ORM\Column(type="datekey")
     * @Assert\NotBlank()
     */
    private $date;

    public function getMember(): ?User
    {
        return $this->member;
    }

    public function getDate(): ?DateKey
    {
        return $this->date;
    }

    public function setMember(User $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = (new DateKey())->setTimestamp($date->getTimestamp());

        return $this;
    }

    public function __toString(): string
    {
        return $this->getMember()->getId().'_'.$this->getDate()->format('Y-m-d');
    }
}
