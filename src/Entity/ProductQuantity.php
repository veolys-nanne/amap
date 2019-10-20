<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductQuantityRepository")
 * @ORM\Table(name="amap_product_quantity")
 */
class ProductQuantity
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @Assert\NotBlank()
     */
    private $product;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Basket", inversedBy="productQuantityCollection", cascade={"persist"})
     * @Assert\NotBlank()
     */
    private $basket;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\NotBlank()
     */
    private $quantity;

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function setBasket(Basket $basket): self
    {
        $this->basket = $basket;

        return $this;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
