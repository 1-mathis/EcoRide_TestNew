<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookingsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingsRepository::class)]
#[ORM\Table(name: 'bookings')]
#[ORM\UniqueConstraint(name: 'uniq_booking_trip_passenger', columns: ['car_sharing_id_id', 'passenger_id'])]
class Bookings
{
    public const STATUS_CONFIRMED           = 'CONFIRMED';
    public const STATUS_CANCELED_BY_DRIVER  = 'CANCELED_BY_DRIVER';
    public const STATUS_CANCELED_BY_PASSENGER = 'CANCELED_BY_PASSENGER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $seatsBooked = 1;

    #[ORM\Column(length: 32)]
    private ?string $status = self::STATUS_CONFIRMED;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?CarSharings $carSharingId = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $passenger = null;

    /** @var Collection<int, PassengerConfirmations> */
    #[ORM\OneToMany(
        mappedBy: 'bookingId',
        targetEntity: PassengerConfirmations::class,
        cascade: ['persist','remove'],
        orphanRemoval: true
    )]
    private Collection $passengerConfirmations;

    public function __construct()
    {
        $this->passengerConfirmations = new ArrayCollection();
        $now = new \DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function __toString(): string
    {
        return sprintf('Booking#%d', $this->id ?? 0);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeatsBooked(): ?int
    {
        return $this->seatsBooked;
    }

    public function setSeatsBooked(int $seatsBooked): self
    {
        $this->seatsBooked = $seatsBooked; return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status; return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt; return $this;
    }

    public function getUpdatedAt(): ?\DateTime 
    { 
        return $this->updatedAt; 
    }

    public function setUpdatedAt(\DateTime $updatedAt): self 
    { 
        $this->updatedAt = $updatedAt; return $this; 
    }

    public function getCarSharingId(): ?CarSharings 
    { 
        return $this->carSharingId; 
    }

    public function setCarSharingId(?CarSharings $carSharingId): self 
    { 
        $this->carSharingId = $carSharingId; return $this; 
    }

    public function getPassenger(): ?User 
    { 
        return $this->passenger; 
    }
    
    public function setPassenger(?User $passenger): self 
    { 
        $this->passenger = $passenger; return $this; 
    }

    /** @return Collection<int, PassengerConfirmations> */
    public function getPassengerConfirmations(): Collection 
    { 
        return $this->passengerConfirmations; 
    }

    public function addPassengerConfirmation(PassengerConfirmations $pc): self
    {
        if (!$this->passengerConfirmations->contains($pc)) {
            $this->passengerConfirmations->add($pc);
            $pc->setBookingId($this);
        }
        return $this;
    }

    public function removePassengerConfirmation(PassengerConfirmations $pc): self
    {
        if ($this->passengerConfirmations->removeElement($pc)) {
            if ($pc->getBookingId() === $this) {
                $pc->setBookingId(null);
            }
        }
        return $this;
    }
}
