<?php

namespace App\Entity\Traits;

use App\Entity\Portfolio;
use Doctrine\ORM\Mapping as ORM;

trait PortfolioTrait
{
    /**
     * @ORM\OneToOne(targetEntity="Portfolio", cascade={"persist"})
     */
    private $portfolio;

    public function getPortfolio(): ?Portfolio
    {
        return $this->portfolio;
    }

    public function setPortfolio(?Portfolio $portfolio): self
    {
        $this->portfolio = $portfolio;

        return $this;
    }
}
