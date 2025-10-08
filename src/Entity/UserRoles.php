<?php

namespace App\Entity;

use App\Repository\UserRolesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRolesRepository::class)]
class UserRoles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userRoles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    /**
     * @var Collection<int, Roles>
     */
    #[ORM\OneToMany(targetEntity: Roles::class, mappedBy: 'userRoles')]
    private Collection $role;

    public function __construct()
    {
        $this->role = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return Collection<int, Roles>
     */
    public function getRole(): Collection
    {
        return $this->role;
    }

    public function addRole(Roles $role): static
    {
        if (!$this->role->contains($role)) {
            $this->role->add($role);
            $role->setUserRoles($this);
        }

        return $this;
    }

    public function removeRole(Roles $role): static
    {
        if ($this->role->removeElement($role)) {
            if ($role->getUserRoles() === $this) {
                $role->setUserRoles(null);
            }
        }

        return $this;
    }
}
