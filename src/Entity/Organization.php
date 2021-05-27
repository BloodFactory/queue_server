<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrganizationRepository::class)
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"parent_id"})
 * }, uniqueConstraints={@ORM\UniqueConstraint(columns={"parent_id", "name"})})
 */
class Organization
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=4000)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="smallint", options={"default": 3})
     */
    private ?int $timezone = 3;

    /**
     * @ORM\ManyToOne(targetEntity=Organization::class, inversedBy="branches")
     */
    private ?Organization $parent;

    /**
     * @ORM\OneToMany(targetEntity=Organization::class, mappedBy="parent")
     */
    private Collection $branches;

    /**
     * @ORM\OneToMany(targetEntity=UserRights::class, mappedBy="organization", orphanRemoval=true)
     */
    private Collection $userRights;

    /**
     * @ORM\OneToMany(targetEntity=Appointment::class, mappedBy="organization", orphanRemoval=true)
     */
    private Collection $appointments;

    public function __construct()
    {
        $this->branches = new ArrayCollection();
        $this->userRights = new ArrayCollection();
        $this->appointments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTimezone(): ?int
    {
        return $this->timezone;
    }

    public function setTimezone(int $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getBranches(): Collection
    {
        return $this->branches;
    }

    public function addBranch(self $branch): self
    {
        if (!$this->branches->contains($branch)) {
            $this->branches[] = $branch;
            $branch->setParent($this);
        }

        return $this;
    }

    public function removeBranch(self $branch): self
    {
        if ($this->branches->removeElement($branch)) {
            // set the owning side to null (unless already changed)
            if ($branch->getParent() === $this) {
                $branch->setParent(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

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
            $userRight->setOrganization($this);
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

    /**
     * @return Collection|Appointment[]
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments[] = $appointment;
            $appointment->setOrganization($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getOrganization() === $this) {
                $appointment->setOrganization(null);
            }
        }

        return $this;
    }
}
