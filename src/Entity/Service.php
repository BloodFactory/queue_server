<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use DateTimeInterface;
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
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="array")
     */
    private array $days = [];

    /**
     * @ORM\Column(type="time")
     */
    private ?DateTimeInterface $receptionTimeFrom;

    /**
     * @ORM\Column(type="time")
     */
    private ?DateTimeInterface $receptionTimeTill;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTimeInterface $restTimeFrom;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTimeInterface $restTimeTill;

    /**
     * @ORM\Column(type="smallint")
     */
    private ?int $duration;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $rest;

    /**
     * @ORM\Column(type="smallint")
     */
    private ?int $persons;

    /**
     * @ORM\ManyToOne(targetEntity=Organization::class, inversedBy="services")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Organization $organization;

    /**
     * @ORM\Column(type="boolean", options={"default": 1})
     */
    private bool $isActive = true;

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

    public function getDays(): ?array
    {
        return $this->days;
    }

    public function setDays(array $days): self
    {
        $this->days = $days;

        return $this;
    }

    public function getReceptionTimeFrom(): ?\DateTimeInterface
    {
        return $this->receptionTimeFrom;
    }

    public function setReceptionTimeFrom(\DateTimeInterface $receptionTimeFrom): self
    {
        $this->receptionTimeFrom = $receptionTimeFrom;

        return $this;
    }

    public function getReceptionTimeTill(): ?\DateTimeInterface
    {
        return $this->receptionTimeTill;
    }

    public function setReceptionTimeTill(\DateTimeInterface $receptionTimeTill): self
    {
        $this->receptionTimeTill = $receptionTimeTill;

        return $this;
    }

    public function getRestTimeFrom(): ?\DateTimeInterface
    {
        return $this->restTimeFrom;
    }

    public function setRestTimeFrom(?\DateTimeInterface $restTimeFrom): self
    {
        $this->restTimeFrom = $restTimeFrom;

        return $this;
    }

    public function getRestTimeTill(): ?\DateTimeInterface
    {
        return $this->restTimeTill;
    }

    public function setRestTimeTill(?\DateTimeInterface $restTimeTill): self
    {
        $this->restTimeTill = $restTimeTill;

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

    public function getRest(): ?int
    {
        return $this->rest;
    }

    public function setRest(?int $rest): self
    {
        $this->rest = $rest;

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

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
