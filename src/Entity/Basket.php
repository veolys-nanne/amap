<?php

namespace App\Entity;

use App\Entity\Traits\SoftDeletableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BasketRepository")
 * @ORM\Table(name="amap_basket")
 */
class Basket
{
    use SoftDeletableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="baskets")
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductQuantity", mappedBy="basket")
     */
    private $productQuantityCollection;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CreditBasketAmount", mappedBy="basket")
     */
    private $creditBasketAmountCollection;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Basket", inversedBy="children")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Basket", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\Column(type="boolean")
     */
    private $frozen = false;

    public function __construct()
    {
        $this->productQuantityCollection = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function getProductQuantityCollection(): ?Collection
    {
        return $this->productQuantityCollection;
    }

    public function getCreditBasketAmountCollection(): ?Collection
    {
        return $this->creditBasketAmountCollection;
    }

    public function getParent(): ?Basket
    {
        return $this->parent;
    }

    public function isFrozen(): ?bool
    {
        return $this->frozen;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function setProductQuantityCollection(array $productQuantityCollection): self
    {
        $this->productQuantityCollection->clear();

        foreach ($productQuantityCollection as $productQuantity) {
            $this->productQuantityCollection->add($productQuantity);
        }

        return $this;
    }

    public function hasProduct(Product $product): bool
    {
        return $this->productQuantityCollection->exists(function ($key, ProductQuantity $existingProductQuantity) use ($product) {
            return $existingProductQuantity->getProduct() == $product;
        });
    }

    public function addProductQuantity(ProductQuantity $productQuantity): self
    {
        if (!$this->hasProduct($productQuantity->getProduct())) {
            $this->productQuantityCollection->add($productQuantity);
        }

        return $this;
    }

    public function addCreditBasketAmount(CreditBasketAmount $creditBasketAmount): self
    {
        $this->creditBasketAmountCollection->add($creditBasketAmount);

        return $this;
    }

    public function setParent(Basket $basket): self
    {
        $this->parent = $basket;

        return $this;
    }

    public function setFrozen(bool $frozen): self
    {
        $this->frozen = $frozen;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getId() ?? '';
    }
}
