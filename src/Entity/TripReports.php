<?php

namespace App\Entity;

use App\Repository\TripReportsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripReportsRepository::class)]
class TripReports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $reason = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTime $handleAt = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'tripReports')]
    private ?carSharings $carSharingsId = null;

    #[ORM\ManyToOne(inversedBy: 'tripReports')]
    private ?user $reporterId = null;

    #[ORM\ManyToOne(inversedBy: 'tripReports')]
    private ?user $driverId = null;

    #[ORM\ManyToOne(inversedBy: 'tripReports')]
    private ?user $handledBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

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

    public function getHandleAt(): ?\DateTime
    {
        return $this->handleAt;
    }

    public function setHandleAt(\DateTime $handleAt): static
    {
        $this->handleAt = $handleAt;

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

    public function getCarSharingsId(): ?carSharings
    {
        return $this->carSharingsId;
    }

    public function setCarSharingsId(?carSharings $carSharingsId): static
    {
        $this->carSharingsId = $carSharingsId;

        return $this;
    }

    public function getReporterId(): ?user
    {
        return $this->reporterId;
    }

    public function setReporterId(?user $reporterId): static
    {
        $this->reporterId = $reporterId;

        return $this;
    }

    public function getDriverId(): ?user
    {
        return $this->driverId;
    }

    public function setDriverId(?user $driverId): static
    {
        $this->driverId = $driverId;

        return $this;
    }

    public function getHandledBy(): ?user
    {
        return $this->handledBy;
    }

    public function setHandledBy(?user $handledBy): static
    {
        $this->handledBy = $handledBy;

        return $this;
    }
}
