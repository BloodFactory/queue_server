<?php

namespace App\Entity\Log;

use App\Entity\User;
use App\Repository\Log\ErrorLogRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ErrorLogRepository::class)
 * @ORM\Table(schema="log", indexes={@ORM\Index(name="usr_idx", columns={"usr_id"})})
 */
class ErrorLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private ?string $id;

    /**
     * @ORM\Column(type="string", length=10000)
     */
    private ?string $message;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $code;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private ?User $usr;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $moment;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUsr(): ?User
    {
        return $this->usr;
    }

    public function setUsr(?User $usr): self
    {
        $this->usr = $usr;

        return $this;
    }

    public function getMoment(): ?DateTimeInterface
    {
        return $this->moment;
    }

    public function setMoment(DateTimeInterface $moment): self
    {
        $this->moment = $moment;

        return $this;
    }
}
