<?php

namespace App\Entity;

use App\Repository\QueueDayRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QueueDayRepository::class)
 */
class QueueDay
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Queue::class, inversedBy="queueDays")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Queue $queue;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTimeInterface $appointmentFrom;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTimeInterface $appointmentTill;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTimeInterface $breakfastFrom;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTimeInterface $breakfastTill;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $duration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $rest;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $persons;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQueue(): ?Queue
    {
        return $this->queue;
    }

    public function setQueue(?Queue $queue): self
    {
        $this->queue = $queue;

        return $this;
    }

    public function getAppointmentFrom(): ?DateTimeInterface
    {
        return $this->appointmentFrom;
    }

    public function setAppointmentFrom(?DateTimeInterface $appointmentFrom): self
    {
        $this->appointmentFrom = $appointmentFrom;

        return $this;
    }

    public function getAppointmentTill(): ?DateTimeInterface
    {
        return $this->appointmentTill;
    }

    public function setAppointmentTill(?DateTimeInterface $appointmentTill): self
    {
        $this->appointmentTill = $appointmentTill;

        return $this;
    }

    public function getBreakfastFrom(): ?DateTimeInterface
    {
        return $this->breakfastFrom;
    }

    public function setBreakfastFrom(?DateTimeInterface $breakfastFrom): self
    {
        $this->breakfastFrom = $breakfastFrom;

        return $this;
    }

    public function getBreakfastTill(): ?DateTimeInterface
    {
        return $this->breakfastTill;
    }

    public function setBreakfastTill(?DateTimeInterface $breakfastTill): self
    {
        $this->breakfastTill = $breakfastTill;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
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

    public function setPersons(?int $persons): self
    {
        $this->persons = $persons;

        return $this;
    }
}
