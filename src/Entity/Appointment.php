<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AppointmentRepository::class)
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="organization_service_date_unique_idx", columns={"organization_service_id", "date"})
 * })
 */
class Appointment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=OrganizationService::class, inversedBy="appointments")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?OrganizationService $organizationService;

    /**
     * @ORM\Column(type="date")
     */
    private ?DateTimeInterface $date;

    /**
     * @ORM\Column(type="time")
     */
    private ?DateTimeInterface $timeFrom;

    /**
     * @ORM\Column(type="time")
     */
    private ?DateTimeInterface $timeTill;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $needDinner;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTimeInterface $dinnerFrom;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTimeInterface $dinnerTill;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $duration;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $persons;

    /**
     * @ORM\OneToMany(targetEntity=Registration::class, mappedBy="appointment", orphanRemoval=true)
     */
    private $registrations;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrganizationService(): ?OrganizationService
    {
        return $this->organizationService;
    }

    public function setOrganizationService(?OrganizationService $organizationService): self
    {
        $this->organizationService = $organizationService;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTimeFrom(): ?DateTimeInterface
    {
        return $this->timeFrom;
    }

    public function setTimeFrom(DateTimeInterface $timeFrom): self
    {
        $this->timeFrom = $timeFrom;
        $this->timeFrom = $timeFrom;

        return $this;
    }

    public function getTimeTill(): ?DateTimeInterface
    {
        return $this->timeTill;
    }

    public function setTimeTill(DateTimeInterface $timeTill): self
    {
        $this->timeTill = $timeTill;

        return $this;
    }

    public function getNeedDinner(): ?bool
    {
        return $this->needDinner;
    }

    public function setNeedDinner(bool $needDinner): self
    {
        $this->needDinner = $needDinner;

        return $this;
    }

    public function getDinnerFrom(): ?DateTimeInterface
    {
        return $this->dinnerFrom;
    }

    public function setDinnerFrom(?DateTimeInterface $dinnerFrom): self
    {
        $this->dinnerFrom = $dinnerFrom;

        return $this;
    }

    public function getDinnerTill(): ?DateTimeInterface
    {
        return $this->dinnerTill;
    }

    public function setDinnerTill(?DateTimeInterface $dinnerTill): self
    {
        $this->dinnerTill = $dinnerTill;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPersons(): ?int
    {
        return $this->persons;
    }

    public function setPersons(int $persons): self
    {
        $this->persons = $persons;

        return $this;
    }

    /**
     * @return Collection|Registration[]
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(Registration $registration): self
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations[] = $registration;
            $registration->setAppointment($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->removeElement($registration)) {
            // set the owning side to null (unless already changed)
            if ($registration->getAppointment() === $this) {
                $registration->setAppointment(null);
            }
        }

        return $this;
    }
}
