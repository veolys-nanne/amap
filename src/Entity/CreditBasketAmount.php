<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CreditBasketAmountRepository")
 * @ORM\Table(name="amap_credit_basket_amount")
 */
class CreditBasketAmount
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Credit")
     * @Assert\NotBlank()
     */
    private $credit;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Basket", inversedBy="creditBasketAmountCollection", cascade={"persist"})
     * @Assert\NotBlank()
     */
    private $basket;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\NotBlank()
     */
    private $amount;

    public function getCredit(): ?Credit
    {
        return $this->credit;
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setCredit(Credit $credit): self
    {
        $this->credit = $credit;

        return $this;
    }

    public function setBasket(Basket $basket): self
    {
        $this->basket = $basket;

        return $this;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
