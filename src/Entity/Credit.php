<?php

namespace App\Entity;

use App\Entity\Traits\SoftDeletableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CreditRepository")
 * @ORM\Table(name="amap_credit")
 */
class Credit
{
    use SoftDeletableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @Assert\NotBlank()
     */
    private $producer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @Assert\NotBlank()
     */
    private $member;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\NotBlank()
     */
    private $totalAmount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\NotBlank()
     */
    private $currentAmount;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $object;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = false;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function getProducer(): ?User
    {
        return $this->producer;
    }

    public function getMember(): ?User
    {
        return $this->member;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function getCurrentAmount(): ?float
    {
        return $this->currentAmount;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }
    public function setProducer(User $producer): self
    {
        $this->producer = $producer;

        return $this;
    }

    public function setMember(User $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function setTotalAmount(float $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function setCurrentAmount(float $currentAmount): self
    {
        $this->currentAmount = $currentAmount;

        return $this;
    }

    public function setObject(string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
