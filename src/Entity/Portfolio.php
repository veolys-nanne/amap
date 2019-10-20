<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="amap_portfolio")
 */
class Portfolio
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Thumbnail", mappedBy="portfolio", cascade={"persist"})
     */
    private $thumbnailCollection;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->thumbnailCollection = new ArrayCollection();
    }

    public function getThumbnailCollection(): ?Collection
    {
        return $this->thumbnailCollection;
    }

    public function setThumbnailCollection(array $thumbnailCollection): self
    {
        $this->thumbnailCollection->clear();
        foreach ($thumbnailCollection as $thumbnail) {
            $this->thumbnailCollection->add($thumbnail);
            $thumbnail->setPortfolio($this);
        }

        return $this;
    }
}
