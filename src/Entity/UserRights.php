<?php

namespace App\Entity;

use App\Repository\UserRightsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRightsRepository::class)
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"usr_id", "organization_id"})})
 */
class UserRights
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userRights")
     * @ORM\JoinColumn(nullable=false, name="usr_id")
     */
    private ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Organization::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Organization $organization;

    /**
     * @ORM\Column(type="boolean", name="v", options={"default": 0})
     */
    private ?bool $view = false;

    /**
     * @ORM\Column(type="boolean", name="e", options={"default": 0})
     */
    private ?bool $edit = false;

    /**
     * @ORM\Column(type="boolean", name="d", options={"default": 0})
     */
    private ?bool $delete = false;

    public function __construct()
    {
        $this->organization = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getView(): ?bool
    {
        return $this->view;
    }

    public function setView(bool $view): self
    {
        $this->view = $view;

        return $this;
    }

    public function getEdit(): ?bool
    {
        return $this->edit;
    }

    public function setEdit(bool $edit): self
    {
        $this->edit = $edit;

        return $this;
    }

    public function getDelete(): ?bool
    {
        return $this->delete;
    }

    public function setDelete(bool $delete): self
    {
        $this->delete = $delete;

        return $this;
    }
}
