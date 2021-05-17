<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="usr")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private ?string $username;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string|null
     * @ORM\Column(type="string")
     */
    private ?string $password;


    /**
     * @ORM\Column(type="boolean", options={"default": 1})
     */
    private bool $isActive = true;

    /**
     * @ORM\OneToOne(targetEntity=UserData::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private ?UserData $userData;

    /**
     * @ORM\OneToOne(targetEntity=UserSettings::class, mappedBy="usr", cascade={"persist", "remove"})
     */
    private ?UserSettings $userSettings;

    /**
     * @ORM\OneToMany(targetEntity=UserRights::class, mappedBy="user", orphanRemoval=true)
     */
    private Collection $userRights;

    public function __construct()
    {
        $this->userData = null;
        $this->userRights = new ArrayCollection();
        $this->userSettings = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getUserData(): ?UserData
    {
        return $this->userData;
    }

    public function setUserData(UserData $userData): self
    {
        // set the owning side of the relation if necessary
        if ($userData->getUser() !== $this) {
            $userData->setUser($this);
        }

        $this->userData = $userData;

        return $this;
    }

    public function getUserSettings(): ?UserSettings
    {
        return $this->userSettings;
    }

    public function setUserSettings(UserSettings $userSettings): self
    {
        // set the owning side of the relation if necessary
        if ($userSettings->getUsr() !== $this) {
            $userSettings->setUsr($this);
        }

        $this->userSettings = $userSettings;

        return $this;
    }

    /**
     * @return Collection|UserRights[]
     */
    public function getUserRights(): Collection
    {
        return $this->userRights;
    }

    public function addUserRight(UserRights $userRight): self
    {
        if (!$this->userRights->contains($userRight)) {
            $this->userRights[] = $userRight;
            $userRight->setUser($this);
        }

        return $this;
    }

    public function removeUserRight(UserRights $userRight): self
    {
        if ($this->userRights->removeElement($userRight)) {
            // set the owning side to null (unless already changed)
            if ($userRight->getUser() === $this) {
                $userRight->setUser(null);
            }
        }

        return $this;
    }
}
