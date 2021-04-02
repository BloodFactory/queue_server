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
     * @ORM\OneToMany(targetEntity=OrganizationRestDay::class, mappedBy="organization")
     */
    private Collection $organizationRestDays;

    public function __construct()
    {
        $this->organizationRestDays = new ArrayCollection();
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
     * @return Collection|OrganizationRestDay[]
     */
    public function getOrganizationRestDays(): Collection
    {
        return $this->organizationRestDays;
    }

    public function addOrganizationRestDay(OrganizationRestDay $organizationRestDay): self
    {
        if (!$this->organizationRestDays->contains($organizationRestDay)) {
            $this->organizationRestDays[] = $organizationRestDay;
            $organizationRestDay->setOrganization($this);
        }

        return $this;
    }

    public function removeOrganizationRestDay(OrganizationRestDay $organizationRestDay): self
    {
        if ($this->organizationRestDays->removeElement($organizationRestDay)) {
            // set the owning side to null (unless already changed)
            if ($organizationRestDay->getOrganization() === $this) {
                $organizationRestDay->setOrganization(null);
            }
        }

        return $this;
    }
}
