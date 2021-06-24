<?php

namespace App\Entity;

use App\Repository\ServiceGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServiceGroupRepository::class)
 */
class ServiceGroup
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\ManyToOne(targetEntity=ServiceGroup::class, inversedBy="serviceGroups")
     */
    private ?ServiceGroup $parent;

    /**
     * @ORM\OneToMany(targetEntity=ServiceGroup::class, mappedBy="parent")
     */
    private Collection $children;

    /**
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="serviceGroup")
     */
    private Collection $services;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->services = new ArrayCollection();
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
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addServiceGroup(self $serviceGroup): self
    {
        if (!$this->children->contains($serviceGroup)) {
            $this->children[] = $serviceGroup;
            $serviceGroup->setParent($this);
        }

        return $this;
    }

    public function removeServiceGroup(self $serviceGroup): self
    {
        if ($this->children->removeElement($serviceGroup)) {
            // set the owning side to null (unless already changed)
            if ($serviceGroup->getParent() === $this) {
                $serviceGroup->setParent(null);
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
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setServiceGroup($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getServiceGroup() === $this) {
                $service->setServiceGroup(null);
            }
        }

        return $this;
    }
}
