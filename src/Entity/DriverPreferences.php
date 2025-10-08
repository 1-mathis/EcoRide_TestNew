<?php

namespace App\Entity;

use App\Repository\DriverPreferencesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: DriverPreferencesRepository::class)]
#[ORM\Table(name: 'driver_preferences')]
#[ORM\UniqueConstraint(name: 'UNIQ_DRIVER_KEY', columns: ['driver_id_id', 'key_name'])]
#[UniqueEntity(fields: ['driverId', 'keyName'], message: 'Cette préférence existe déjà.')]
class DriverPreferences
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $keyName = null;

    #[ORM\Column(length: 120)]
    private ?string $valueText = null;

    #[ORM\ManyToOne(inversedBy: 'driverPreferences')]
    private ?user $driverId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKeyName(): ?string
    {
        return $this->keyName;
    }

    public function setKeyName(string $keyName): static
    {
        $this->keyName = $keyName;

        return $this;
    }

    public function getValueText(): ?string
    {
        return $this->valueText;
    }

    public function setValueText(string $valueText): static
    {
        $this->valueText = $valueText;

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
}
