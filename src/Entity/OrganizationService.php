<?php

namespace App\Entity;

use App\Repository\OrganizationServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrganizationServiceRepository::class)
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="organization_service_unique_idx", columns={"organization_id", "service_id"})
 * })
 */
class OrganizationService
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Organization::class, inversedBy="organizationServices")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Organization $organization;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Service $service;

    /**
     * @ORM\OneToMany(targetEntity=Appointment::class, mappedBy="organizationService", orphanRemoval=true)
     */
    private Collection $appointments;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

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
            $appointment->setOrganizationService($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getOrganizationService() === $this) {
                $appointment->setOrganizationService(null);
            }
        }

        return $this;
    }
}
