<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(schema="public")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=180, unique=true, nullable=true)
     */
    private ?string $email;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private ?string $company_name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $create_at;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $password;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="employee")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?User $chief;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="chief")
     */
    private $employees;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $first_name;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $last_name;

    public function __construct() {
        $this->employees = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getCompanyName() : ?string
    {
        return $this->company_name;
    }

    public function setCompanyName(?string $company_name): self
    {
        $this->company_name = $company_name;

        return $this;
    }

    public function getCreateAt(): ?DateTime
    {
        return $this->create_at;
    }

    public function setCreateAt(?DateTime $dateTime = null): self
    {
        $this->create_at = $dateTime ?? new DateTime();

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getChief() : ?User
    {
        return $this->chief;
    }

    public function setChief(?User $chief) : self
    {
        return $this->chief = $chief;
    }

    public function getEmployees() : ArrayCollection
    {
        return $this->employees;
    }

    public function __toString(): string
    {
        return strtoupper($this->getLastName()) . ' ' . ucfirst(strtolower($this->getFirstName()));
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }


}
