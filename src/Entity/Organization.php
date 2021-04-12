<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrganizationRepository::class)
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

    public function __construct()
    {
        $this->organizationServices = new ArrayCollection();
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
}
