<?php

namespace App\Entity;

use App\Entity\Traits\PortfolioTrait;
use App\Entity\Traits\SoftDeletableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="amap_product")
 */
class Product
{
    use PortfolioTrait, SoftDeletableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(name="product_order", type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(0)
     */
    private $order;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @Assert\NotBlank()
     */
    private $producer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function getProducer(): ?User
    {
        return $this->producer;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function setActive($active): self
    {
        $this->active = $active;

        return $this;
    }

    public function setProducer(User $user): self
    {
        $this->producer = $user;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getId();
    }
}
