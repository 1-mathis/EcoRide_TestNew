<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private ?string $slug = null;

    #[ORM\Column(length: 60)]
    private ?string $brand = null;

    #[ORM\Column(length: 60)]
    private ?string $model = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $color = null;

    #[ORM\Column]
    private ?int $seats = null;

    #[ORM\Column(length: 20)]
    private ?string $plate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $firstRegistration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $ownerId = null;

    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?VehicleEnergies $energy = null;

    /**
     * @var Collection<int, CarSharings>
     */
    #[ORM\OneToMany(targetEntity: CarSharings::class, mappedBy: 'vehicleId')]
    private Collection $carSharings;

    public function __construct()
    {
        $this->carSharings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function setSeats(int $seats): static
    {
        $this->seats = $seats;
        return $this;
    }

    public function getPlate(): ?string
    {
        return $this->plate;
    }

    public function setPlate(string $plate): static
    {
        $this->plate = $plate;
        return $this;
    }

    public function getFirstRegistration(): ?\DateTimeInterface
    {
        return $this->firstRegistration;
    }

    public function setFirstRegistration(\DateTimeInterface $firstRegistration): static
    {
        $this->firstRegistration = $firstRegistration;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getOwnerId(): ?User
    {
        return $this->ownerId;
    }

    public function setOwnerId(?User $ownerId): static
    {
        $this->ownerId = $ownerId;
        return $this;
    }

    public function getEnergy(): ?VehicleEnergies
    {
        return $this->energy;
    }

    public function setEnergy(?VehicleEnergies $energy): static
    {
        $this->energy = $energy;
        return $this;
    }

    /** @return Collection<int, CarSharings> */
    public function getCarSharings(): Collection
    {
        return $this->carSharings;
    }

    public function addCarSharing(CarSharings $carSharing): static
    {
        if (!$this->carSharings->contains($carSharing)) {
            $this->carSharings->add($carSharing);
            $carSharing->setVehicleId($this);
        }
        return $this;
    }

    public function removeCarSharing(CarSharings $carSharing): static
    {
        if ($this->carSharings->removeElement($carSharing)) {
            if ($carSharing->getVehicleId() === $this) {
                $carSharing->setVehicleId(null);
            }
        }
        return $this;
    }
}
