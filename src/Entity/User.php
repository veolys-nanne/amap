<?php
namespace App\Entity;

use App\Entity\Traits\SoftDeletableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\Traits\PortfolioTrait;

/**
 * @UniqueEntity(fields="email")
 * @ORM\Table(name="amap_user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    use PortfolioTrait, SoftDeletableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="array")
     * @Assert\All({
     *     @Assert\Email()
     * })
     */
    private $broadcastList = [];

    /**
     * @ORM\Column(name="user_order", type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(0)
     */
    private $order;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $denomination;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $payto;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"registration"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $zipCode;

    /**
     * @ORM\Column(type="array")
     */
    private $phoneNumbers = [];

    /**
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(max=250)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetPassword;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = true;

    /**
     * @ORM\Column(type="boolean", options={"default" : 1})
     */
    private $new = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $parent;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Basket", mappedBy="user")
     */
    private $baskets;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $deleveries = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getResetPassword(): ?string
    {
        return $this->resetPassword;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getBroadcastList(): ?array
    {
        return $this->broadcastList ? $this->broadcastList : [];
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function getPayto(): ?string
    {
        return $this->payto;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function getPhoneNumbers(): ?array
    {
        return $this->phoneNumbers ? $this->phoneNumbers : [];
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function isNew(): ?bool
    {
        return $this->new;
    }

    public function getParent(): ?User
    {
        return $this->parent;
    }

    public function getRoles(): array
    {
        return $this->roles ? $this->roles : [];
    }

    public function getDeleveries(): ?array
    {
        return $this->deleveries;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setResetPassword(string $resetPassword): self
    {
        $this->resetPassword = $resetPassword;

        return $this;
    }

    public function eraseCredentials()
    {
    }

    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setBroadcastList(?array $broadcastList): self
    {
        $this->broadcastList = $broadcastList;

        return $this;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function setDenomination($denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }

    public function setPayto($payto): self
    {
        $this->payto = $payto;

        return $this;
    }

    public function setFirstname($firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function setLastname($lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function setAddress($address): self
    {
        $this->address = $address;

        return $this;
    }

    public function setCity($city): self
    {
        $this->city = $city;

        return $this;
    }

    public function setZipCode($zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function setPhoneNumbers(?array $phoneNumbers): self
    {
        $this->phoneNumbers = $phoneNumbers;

        return $this;
    }

    public function setPlainPassword($plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function setActive($active): self
    {
        $this->active = $active;

        return $this;
    }

    public function setNew($new): self
    {
        $this->new = $new;

        return $this;
    }

    public function setParent(User $user): self
    {
        $this->parent = $user;

        return $this;
    }

    public function addRole($role): self
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole($role): self
    {
        if (in_array($role, $this->roles)) {
            array_splice($this->roles, array_search($role, $this->roles), 1);
        }

        return $this;
    }

    public function setDeleveries(array $deleveries): self
    {
        $this->deleveries = $deleveries;

        return $this;
    }

    public function __toString()
    {
        return in_array('ROLE_PRODUCER', $this->getRoles()) && $this->getDenomination() ? $this->getDenomination() : $this->getLastname().(!empty($this->getFirstname()) ? ' '.$this->getFirstname() : '');
    }
}
