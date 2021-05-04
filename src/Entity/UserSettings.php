<?php

namespace App\Entity;

use App\Repository\UserSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserSettingsRepository::class)
 */
class UserSettings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="userSettings", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $usr;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private ?bool $darkMode = false;

    public function __construct()
    {
        $this->usr = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsr(): ?User
    {
        return $this->usr;
    }

    public function setUsr(User $usr): self
    {
        $this->usr = $usr;

        return $this;
    }

    public function getDarkMode(): ?bool
    {
        return $this->darkMode;
    }

    public function setDarkMode(bool $darkMode): self
    {
        $this->darkMode = $darkMode;

        return $this;
    }
}
