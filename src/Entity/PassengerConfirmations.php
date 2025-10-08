<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PassengerConfirmationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PassengerConfirmationsRepository::class)]
#[ORM\Table(name: 'passenger_confirmations')]
class PassengerConfirmations
{
    public const STATUS_PENDING   = 'PENDING';
    public const STATUS_CONFIRMED = 'CONFIRMED';
    public const STATUS_REPORTED  = 'REPORTED';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $token = null;

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $confirmedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'passengerConfirmations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Bookings $bookingId = null;

    public function __construct()
    {
        $this->token = bin2hex(random_bytes(16));
        $now = new \DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function __toString(): string
    {
        return sprintf('PassengerConfirmation#%d', $this->id ?? 0);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token; return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status; return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment; return $this;
    }

    public function getConfirmedAt(): ?\DateTime
    {
        return $this->confirmedAt;
    }
    public function setConfirmedAt(?\DateTime $confirmedAt): self
    {
        $this->confirmedAt = $confirmedAt; return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt; return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt; return $this;
    }

    public function getBookingId(): ?Bookings
    {
        return $this->bookingId;
    }

    public function setBookingId(?Bookings $bookingId): self
    {
        $this->bookingId = $bookingId; return $this;
    }

    public function confirmNow(): self
    {
        $this->status = self::STATUS_CONFIRMED;
        $this->confirmedAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function reportWithComment(string $comment): self
    {
        $this->status = self::STATUS_REPORTED;
        $this->comment = $comment;
        $this->updatedAt = new \DateTime();
        return $this;
    }
}
