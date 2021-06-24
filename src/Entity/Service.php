<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
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
     * @ORM\OneToMany(targetEntity=Appointment::class, mappedBy="service", orphanRemoval=true)
     */
    private Collection $appointments;

    /**
     * @ORM\OneToMany(targetEntity=AppointmentTemplate::class, mappedBy="service", orphanRemoval=true)
     */
    private Collection $appointmentTemplates;

    /**
     * @ORM\ManyToOne(targetEntity=ServiceGroup::class, inversedBy="services")
     */
    private ?ServiceGroup $serviceGroup;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
        $this->appointmentTemplates = new ArrayCollection();
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
            $appointment->setService($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getService() === $this) {
                $appointment->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AppointmentTemplate[]
     */
    public function getAppointmentTemplates(): Collection
    {
        return $this->appointmentTemplates;
    }

    public function addAppointmentTemplate(AppointmentTemplate $appointmentTemplate): self
    {
        if (!$this->appointmentTemplates->contains($appointmentTemplate)) {
            $this->appointmentTemplates[] = $appointmentTemplate;
            $appointmentTemplate->setService($this);
        }

        return $this;
    }

    public function removeAppointmentTemplate(AppointmentTemplate $appointmentTemplate): self
    {
        if ($this->appointmentTemplates->removeElement($appointmentTemplate)) {
            // set the owning side to null (unless already changed)
            if ($appointmentTemplate->getService() === $this) {
                $appointmentTemplate->setService(null);
            }
        }

        return $this;
    }

    public function getServiceGroup(): ?ServiceGroup
    {
        return $this->serviceGroup;
    }

    public function setServiceGroup(?ServiceGroup $serviceGroup): self
    {
        $this->serviceGroup = $serviceGroup;

        return $this;
    }
}
