<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Thumbnail", mappedBy="portfolio", cascade={"all"}, orphanRemoval=true)
     * @Assert\Valid
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

    public function addThumbnailCollection(Thumbnail $thumbnail): self
    {
        $thumbnail->setPortfolio($this);
        $this->thumbnailCollection->add($thumbnail);

        return $this;
    }

    public function removeThumbnailCollection(Thumbnail $thumbnail): self
    {
        $this->thumbnailCollection->removeElement($thumbnail);
        $thumbnail->setPortfolio(null);

        return $this;
    }
}
