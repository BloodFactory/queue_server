<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"service_group_id", "name"})})
 */
class Service
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
     * @ORM\ManyToOne(targetEntity=ServicesGroup::class, inversedBy="services")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?ServicesGroup $serviceGroup;

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

    public function getServiceGroup(): ?ServicesGroup
    {
        return $this->serviceGroup;
    }

    public function setServiceGroup(?ServicesGroup $serviceGroup): self
    {
        $this->serviceGroup = $serviceGroup;

        return $this;
    }
}
