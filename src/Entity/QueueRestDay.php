<?php

namespace App\Entity;

use App\Repository\QueueRestDayRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QueueRestDayRepository::class)
 */
class QueueRestDay
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Queue::class, inversedBy="queueRestDays")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Queue $queue;

    /**
     * @ORM\Column(type="date")
     */
    private ?DateTimeInterface $day;

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

    public function getDay(): ?DateTimeInterface
    {
        return $this->day;
    }

    public function setDay(DateTimeInterface $day): self
    {
        $this->day = $day;

        return $this;
    }
}
