<?php

namespace App\Entity\Traits;

use App\Entity\Portfolio;
use Doctrine\ORM\Mapping as ORM;

Trait SoftDeletableTrait {
    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $deleted = false;

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }
}