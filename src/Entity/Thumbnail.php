<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @ORM\Table(name="amap_thumbnail")
 * @Vich\Uploadable
 */
class Thumbnail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Vich\UploadableField(mapping="thumbnail", fileNameProperty="media")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $media;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Portfolio", inversedBy="thumbnailCollection")
     */
    private $portfolio;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPortfolio(): ?Portfolio
    {
        return $this->portfolio;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setMedia(?string $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function setFile(?File $file = null): self
    {
        $this->file = $file;

        if ($this->file instanceof UploadedFile) {
            $this->setUdatedAt(new \DateTime('now'));
        }

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setPortfolio(?Portfolio $portfolio): self
    {
        $this->portfolio = $portfolio;

        return $this;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
