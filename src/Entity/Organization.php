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
 * })
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
     * @ORM\Column(type="string", length=4000, unique=true)
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

    public function __construct()
    {
        $this->organizationServices = new ArrayCollection();
        $this->branches = new ArrayCollection();
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
}
