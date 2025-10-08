<?php

namespace App\Entity;

use App\Repository\CarSharingsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarSharingsRepository::class)]
class CarSharings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 160)]
    private ?string $slug = null;

    #[ORM\Column(length: 120)]
    private ?string $fromCity = null;

    #[ORM\Column(length: 120)]
    private ?string $toCity = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $departureAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $arrivalAt = null;

    #[ORM\Column]
    private ?int $durationMinutes = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\PositiveOrZero(message: 'Le nombre de places doit être au minimum 0.')]
    private ?int $seatsTotal = null;

    #[ORM\Column]
    private ?int $seatsRemaining = null;

    #[ORM\Column]
    private ?bool $isEco = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'carSharings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $driverId = null;

    #[ORM\ManyToOne(inversedBy: 'carSharings')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Vehicle $vehicleId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $finishedAt = null;

    /**
     * @var Collection<int, Bookings>
     */
    #[ORM\OneToMany(targetEntity: Bookings::class, mappedBy: 'carSharingId')]
    private Collection $bookings;

    #[ORM\ManyToOne(inversedBy: 'relatedCsId')]
    private ?CreditsLedger $creditsLedger = null;

    /**
     * @var Collection<int, Reviews>
     */
    #[ORM\OneToMany(targetEntity: Reviews::class, mappedBy: 'carSharingsId')]
    private Collection $reviews;

    /**
     * @var Collection<int, TripReports>
     */
    #[ORM\OneToMany(targetEntity: TripReports::class, mappedBy: 'carSharingsId')]
    private Collection $tripReports;

    public function __construct()
    {
        $this->bookings    = new ArrayCollection();
        $this->reviews     = new ArrayCollection();
        $this->tripReports = new ArrayCollection();
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

    public function getFromCity(): ?string
    {
        return $this->fromCity;
    }

    public function setFromCity(string $fromCity): static
    {
        $this->fromCity = $fromCity;
        return $this;
    }

    public function getToCity(): ?string
    {
        return $this->toCity;
    }

    public function setToCity(string $toCity): static
    {
        $this->toCity = $toCity;
        return $this;
    }

    public function getDepartureAt(): ?\DateTimeInterface
    {
        return $this->departureAt;
    }

    public function setDepartureAt(\DateTimeInterface $departureAt): static
    {
        $this->departureAt = $departureAt;
        return $this;
    }

    public function getArrivalAt(): ?\DateTimeInterface
    {
        return $this->arrivalAt;
    }

    public function setArrivalAt(\DateTimeInterface $arrivalAt): static
    {
        $this->arrivalAt = $arrivalAt;
        return $this;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(int $durationMinutes): static
    {
        $this->durationMinutes = $durationMinutes;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getSeatsTotal(): ?int
    {
        return $this->seatsTotal;
    }

    public function setSeatsTotal(int $seatsTotal): static
    {
        $this->seatsTotal = $seatsTotal;
        return $this;
    }

    public function getSeatsRemaining(): ?int
    {
        return $this->seatsRemaining;
    }

    public function setSeatsRemaining(int $seatsRemaining): static
    {
        $this->seatsRemaining = $seatsRemaining;
        return $this;
    }

    public function isEco(): ?bool
    {
        return $this->isEco;
    }

    public function setIsEco(bool $isEco): static
    {
        $this->isEco = $isEco;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
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

    public function getDriverId(): ?User
    {
        return $this->driverId;
    }

    public function setDriverId(?User $driverId): static
    {
        $this->driverId = $driverId;
        return $this;
    }

    public function getVehicleId(): ?Vehicle
    {
        return $this->vehicleId;
    }

    public function setVehicleId(?Vehicle $vehicleId): static
    {
        $this->vehicleId = $vehicleId;
        return $this;
    }

    /** @return Collection<int, Bookings> */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Bookings $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setCarSharingId($this);
        }
        return $this;
    }

    public function removeBooking(Bookings $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            if ($booking->getCarSharingId() === $this) {
                $booking->setCarSharingId(null);
            }
        }
        return $this;
    }

    public function getCreditsLedger(): ?CreditsLedger
    {
        return $this->creditsLedger;
    }

    public function setCreditsLedger(?CreditsLedger $creditsLedger): static
    {
        $this->creditsLedger = $creditsLedger;
        return $this;
    }

    /** @return Collection<int, Reviews> */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Reviews $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setCarSharingsId($this);
        }
        return $this;
    }

    public function removeReview(Reviews $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getCarSharingsId() === $this) {
                $review->setCarSharingsId(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, TripReports> */
    public function getTripReports(): Collection
    {
        return $this->tripReports;
    }

    public function addTripReport(TripReports $tripReport): static
    {
        if (!$this->tripReports->contains($tripReport)) {
            $this->tripReports->add($tripReport);
            $tripReport->setCarSharingsId($this);
        }
        return $this;
    }

    public function removeTripReport(TripReports $tripReport): static
    {
        if ($this->tripReports->removeElement($tripReport)) {
            if ($tripReport->getCarSharingsId() === $this) {
                $tripReport->setCarSharingsId(null);
            }
        }
        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): static
    {
        $this->startedAt = $startedAt; return $this;
    }

    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt;
    }
    
    public function setFinishedAt(?\DateTimeInterface $finishedAt): static
    {
        $this->finishedAt = $finishedAt; return $this;
    }
}
