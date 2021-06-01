<?php

namespace App\Entity;

use App\Repository\AppointmentTemplateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AppointmentTemplateRepository::class)
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"service_id", "organization_id"})
 * })
 */
class AppointmentTemplate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Organization::class, inversedBy="appointmentTemplates")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Organization $organization;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="appointmentTemplates")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Service $service;

    /**
     * @ORM\Column(type="time")
     */
    private ?\DateTimeInterface $timeFrom;

    /**
     * @ORM\Column(type="time")
     */
    private ?\DateTimeInterface $timeTill;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $needDinner;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?\DateTimeInterface $dinnerFrom;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?\DateTimeInterface $dinnerTill;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $duration;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $persons;

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

    public function getTimeFrom(): ?\DateTimeInterface
    {
        return $this->timeFrom;
    }

    public function setTimeFrom(\DateTimeInterface $timeFrom): self
    {
        $this->timeFrom = $timeFrom;

        return $this;
    }

    public function getTimeTill(): ?\DateTimeInterface
    {
        return $this->timeTill;
    }

    public function setTimeTill(\DateTimeInterface $timeTill): self
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

    public function getDinnerFrom(): ?\DateTimeInterface
    {
        return $this->dinnerFrom;
    }

    public function setDinnerFrom(?\DateTimeInterface $dinnerFrom): self
    {
        $this->dinnerFrom = $dinnerFrom;

        return $this;
    }

    public function getDinnerTill(): ?\DateTimeInterface
    {
        return $this->dinnerTill;
    }

    public function setDinnerTill(?\DateTimeInterface $dinnerTill): self
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
}
