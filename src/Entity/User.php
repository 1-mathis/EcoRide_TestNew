<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 60)]
    private ?string $username = null;

    #[ORM\Column(length: 80)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?int $credit = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 2)]
    private ?string $avgRating = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $suspendedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $suspendedReason = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    /**
     * @var Collection<int, UserRoles>
     */
    #[ORM\OneToMany(targetEntity: UserRoles::class, mappedBy: 'userId')]
    private Collection $userRoles;

    /**
     * @var Collection<int, Vehicle>
     */
    #[ORM\OneToMany(targetEntity: Vehicle::class, mappedBy: 'ownerId')]
    private Collection $vehicles;

    /**
     * @var Collection<int, DriverPreferences>
     */
    #[ORM\OneToMany(targetEntity: DriverPreferences::class, mappedBy: 'driverId')]
    private Collection $driverPreferences;

    /**
     * @var Collection<int, CarSharings>
     */
    #[ORM\OneToMany(targetEntity: CarSharings::class, mappedBy: 'driverId')]
    private Collection $carSharings;

    #[ORM\ManyToOne(inversedBy: 'passengerId')]
    private ?Bookings $bookings = null;

    /**
     * @var Collection<int, CreditsLedger>
     */
    #[ORM\OneToMany(targetEntity: CreditsLedger::class, mappedBy: 'userId')]
    private Collection $creditsLedgers;

    /**
     * @var Collection<int, Reviews>
     */
    #[ORM\OneToMany(targetEntity: Reviews::class, mappedBy: 'driverId')]
    private Collection $reviews;

    /**
     * @var Collection<int, TripReports>
     */
    #[ORM\OneToMany(targetEntity: TripReports::class, mappedBy: 'reporterId')]
    private Collection $tripReports;

    /**
     * @var Collection<int, ModerationsActions>
     */
    #[ORM\OneToMany(targetEntity: ModerationsActions::class, mappedBy: 'actorId')]
    private Collection $moderationsActions;

    /**
     * @var Collection<int, Notifications>
     */
    #[ORM\OneToMany(targetEntity: Notifications::class, mappedBy: 'userId')]
    private Collection $notifications;

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->vehicles = new ArrayCollection();
        $this->driverPreferences = new ArrayCollection();
        $this->carSharings = new ArrayCollection();
        $this->creditsLedgers = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->tripReports = new ArrayCollection();
        $this->moderationsActions = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
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

    public function getCredit(): ?int
    {
        return $this->credit;
    }

    public function setCredit(int $credit): static
    {
        $this->credit = $credit;

        return $this;
    }

    public function getAvgRating(): ?string
    {
        return $this->avgRating;
    }

    public function setAvgRating(string $avgRating): static
    {
        $this->avgRating = $avgRating;

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

    public function getSuspendedAt(): ?\DateTimeInterface
    {
        return $this->suspendedAt;
    }

    public function setSuspendedAt(?\DateTimeInterface $suspendedAt): static
    {
        $this->suspendedAt = $suspendedAt;
        return $this;
    }

    public function getSuspendedReason(): ?string
    {
        return $this->suspendedReason;
    }

    public function setSuspendedReason(?string $suspendedReason): static
    {
        $this->suspendedReason = $suspendedReason;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, UserRoles>
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(UserRoles $userRole): static
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles->add($userRole);
            $userRole->setUserId($this);
        }

        return $this;
    }

    public function removeUserRole(UserRoles $userRole): static
    {
        if ($this->userRoles->removeElement($userRole)) {
            if ($userRole->getUserId() === $this) {
                $userRole->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vehicle>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicle $vehicle): static
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles->add($vehicle);
            $vehicle->setOwnerId($this);
        }

        return $this;
    }

    public function removeVehicle(Vehicle $vehicle): static
    {
        if ($this->vehicles->removeElement($vehicle)) {
            if ($vehicle->getOwnerId() === $this) {
                $vehicle->setOwnerId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DriverPreferences>
     */
    public function getDriverPreferences(): Collection
    {
        return $this->driverPreferences;
    }

    public function addDriverPreference(DriverPreferences $driverPreference): static
    {
        if (!$this->driverPreferences->contains($driverPreference)) {
            $this->driverPreferences->add($driverPreference);
            $driverPreference->setDriverId($this);
        }

        return $this;
    }

    public function removeDriverPreference(DriverPreferences $driverPreference): static
    {
        if ($this->driverPreferences->removeElement($driverPreference)) {
            if ($driverPreference->getDriverId() === $this) {
                $driverPreference->setDriverId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CarSharings>
     */
    public function getCarSharings(): Collection
    {
        return $this->carSharings;
    }

    public function addCarSharing(CarSharings $carSharing): static
    {
        if (!$this->carSharings->contains($carSharing)) {
            $this->carSharings->add($carSharing);
            $carSharing->setDriverId($this);
        }

        return $this;
    }

    public function removeCarSharing(CarSharings $carSharing): static
    {
        if ($this->carSharings->removeElement($carSharing)) {
            if ($carSharing->getDriverId() === $this) {
                $carSharing->setDriverId(null);
            }
        }

        return $this;
    }

    public function getBookings(): ?Bookings
    {
        return $this->bookings;
    }

    public function setBookings(?Bookings $bookings): static
    {
        $this->bookings = $bookings;

        return $this;
    }

    /**
     * @return Collection<int, CreditsLedger>
     */
    public function getCreditsLedgers(): Collection
    {
        return $this->creditsLedgers;
    }

    public function addCreditsLedger(CreditsLedger $creditsLedger): static
    {
        if (!$this->creditsLedgers->contains($creditsLedger)) {
            $this->creditsLedgers->add($creditsLedger);
            $creditsLedger->setUserId($this);
        }

        return $this;
    }

    public function removeCreditsLedger(CreditsLedger $creditsLedger): static
    {
        if ($this->creditsLedgers->removeElement($creditsLedger)) {
            if ($creditsLedger->getUserId() === $this) {
                $creditsLedger->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reviews>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Reviews $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setDriverId($this);
        }

        return $this;
    }

    public function removeReview(Reviews $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getDriverId() === $this) {
                $review->setDriverId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TripReports>
     */
    public function getTripReports(): Collection
    {
        return $this->tripReports;
    }

    public function addTripReport(TripReports $tripReport): static
    {
        if (!$this->tripReports->contains($tripReport)) {
            $this->tripReports->add($tripReport);
            $tripReport->setReporterId($this);
        }

        return $this;
    }

    public function removeTripReport(TripReports $tripReport): static
    {
        if ($this->tripReports->removeElement($tripReport)) {
            if ($tripReport->getReporterId() === $this) {
                $tripReport->setReporterId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ModerationsActions>
     */
    public function getModerationsActions(): Collection
    {
        return $this->moderationsActions;
    }

    public function addModerationsAction(ModerationsActions $moderationsAction): static
    {
        if (!$this->moderationsActions->contains($moderationsAction)) {
            $this->moderationsActions->add($moderationsAction);
            $moderationsAction->setActorId($this);
        }

        return $this;
    }

    public function removeModerationsAction(ModerationsActions $moderationsAction): static
    {
        if ($this->moderationsActions->removeElement($moderationsAction)) {
            if ($moderationsAction->getActorId() === $this) {
                $moderationsAction->setActorId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notifications>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notifications $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUserId($this);
        }

        return $this;
    }

    public function removeNotification(Notifications $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getUserId() === $this) {
                $notification->setUserId(null);
            }
        }

        return $this;
    }
}
