<?php

namespace App\Entity;

use App\Entity\Traits\SoftDeletableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlanningRepository")
 * @ORM\Table(name="amap_planning")
 */
class Planning
{
    use SoftDeletableTrait;

    const STATE_INACTIVE = 0;
    const STATE_OPEN = 1;
    const STATE_CLOSE = 2;
    const STATE_ONLINE = 3;
    const LABELS = [
        self::STATE_INACTIVE => 'Paramétrage',
        self::STATE_OPEN => 'Saisie des disponibilités',
        self::STATE_CLOSE => 'Création du planning définitif',
        self::STATE_ONLINE => 'En ligne',
    ];
    const TRANSITIONS = [
        self::STATE_INACTIVE => self::STATE_OPEN,
        self::STATE_OPEN => self::STATE_CLOSE,
        self::STATE_CLOSE => self::STATE_ONLINE,
        self::STATE_ONLINE => false,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlanningElement", mappedBy="planning", cascade={"all"})
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $elements;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AvailabilitySchedule", mappedBy="planning")
     */
    private $availabilitySchedules;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $state = 0;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getElements(): ?Collection
    {
        return $this->elements;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function getNextState(): ?int
    {
        return self::TRANSITIONS[$this->state];
    }

    public function setElements(array $elements): self
    {
        $this->elements->clear();

        foreach ($elements as $element) {
            $element->setPlanning($this);
            $this->elements->add($element);
        }

        return $this;
    }

    public function addElement(PlanningElement $element): self
    {
        foreach ($this->elements as $currentElement) {
            if ($currentElement->getDate() == $element->getDate()) {
                $currentElement->setMembers($element->getMembers()->toArray());

                return $this;
            }
        }

        $element->setPlanning($this);
        $this->elements->add($element);

        return $this;
    }

    public function setState($state): self
    {
        $this->state = $state;

        return $this;
    }
}
