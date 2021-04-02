<?php

namespace App\Entity;

use App\Repository\QueueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QueueRepository::class)
 */
class Queue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Service $service;

    /**
     * @ORM\ManyToOne(targetEntity=Organization::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Organization $organization;

    /**
     * @ORM\OneToOne(targetEntity=QueueDefault::class, mappedBy="queue", cascade={"persist", "remove"})
     */
    private ?QueueDefault $queueDefault;

    /**
     * @ORM\OneToMany(targetEntity=QueueDay::class, mappedBy="queue")
     */
    private Collection $queueDays;

    /**
     * @ORM\OneToMany(targetEntity=QueueRestDay::class, mappedBy="queue", orphanRemoval=true)
     */
    private Collection $queueRestDays;

    public function __construct()
    {
        $this->queueDays = new ArrayCollection();
        $this->queueRestDays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getQueueDefault(): ?QueueDefault
    {
        return $this->queueDefault;
    }

    public function setQueueDefault(QueueDefault $queueDefault): self
    {
        // set the owning side of the relation if necessary
        if ($queueDefault->getQueue() !== $this) {
            $queueDefault->setQueue($this);
        }

        $this->queueDefault = $queueDefault;

        return $this;
    }

    /**
     * @return Collection|QueueDay[]
     */
    public function getQueueDays(): Collection
    {
        return $this->queueDays;
    }

    public function addQueueDay(QueueDay $queueDay): self
    {
        if (!$this->queueDays->contains($queueDay)) {
            $this->queueDays[] = $queueDay;
            $queueDay->setQueue($this);
        }

        return $this;
    }

    public function removeQueueDay(QueueDay $queueDay): self
    {
        if ($this->queueDays->removeElement($queueDay)) {
            // set the owning side to null (unless already changed)
            if ($queueDay->getQueue() === $this) {
                $queueDay->setQueue(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|QueueRestDay[]
     */
    public function getQueueRestDays(): Collection
    {
        return $this->queueRestDays;
    }

    public function addQueueRestDay(QueueRestDay $queueRestDay): self
    {
        if (!$this->queueRestDays->contains($queueRestDay)) {
            $this->queueRestDays[] = $queueRestDay;
            $queueRestDay->setQueue($this);
        }

        return $this;
    }

    public function removeQueueRestDay(QueueRestDay $queueRestDay): self
    {
        if ($this->queueRestDays->removeElement($queueRestDay)) {
            // set the owning side to null (unless already changed)
            if ($queueRestDay->getQueue() === $this) {
                $queueRestDay->setQueue(null);
            }
        }

        return $this;
    }
}
