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
     * @ORM\OneToMany(targetEntity=OrganizationService::class, mappedBy="organization", orphanRemoval=true)
     */
    private Collection $organizationServices;

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
     * @ORM\OneToMany(targetEntity=Department::class, mappedBy="organization", orphanRemoval=true)
     */
    private Collection $departments;

    /**
     * @ORM\OneToMany(targetEntity=UserRights::class, mappedBy="user", orphanRemoval=true)
     */
    private Collection $userRights;

    public function __construct()
    {
        $this->organizationServices = new ArrayCollection();
        $this->branches = new ArrayCollection();
        $this->departments = new ArrayCollection();
        $this->userRights = new ArrayCollection();
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

    /**
     * @return Collection|OrganizationService[]
     */
    public function getOrganizationServices(): Collection
    {
        return $this->organizationServices;
    }

    public function addOrganizationService(OrganizationService $organizationService): self
    {
        if (!$this->organizationServices->contains($organizationService)) {
            $this->organizationServices[] = $organizationService;
            $organizationService->setOrganization($this);
        }

        return $this;
    }

    public function removeOrganizationService(OrganizationService $organizationService): self
    {
        if ($this->organizationServices->removeElement($organizationService)) {
            // set the owning side to null (unless already changed)
            if ($organizationService->getOrganization() === $this) {
                $organizationService->setOrganization(null);
            }
        }

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
     * @return Collection|Department[]
     */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): self
    {
        if (!$this->departments->contains($department)) {
            $this->departments[] = $department;
            $department->setOrganization($this);
        }

        return $this;
    }

    public function removeDepartment(Department $department): self
    {
        if ($this->departments->removeElement($department)) {
            // set the owning side to null (unless already changed)
            if ($department->getOrganization() === $this) {
                $department->setOrganization(null);
            }
        }

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
}
