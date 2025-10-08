<?php

namespace App\Entity;

use App\Repository\CreditsLedgerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreditsLedgerRepository::class)]
class CreditsLedger
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $direction = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column(length: 80)]
    private ?string $reason = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'creditsLedgers')]
    private ?user $userId = null;

    /**
     * @var Collection<int, carSharings>
     */
    #[ORM\OneToMany(targetEntity: carSharings::class, mappedBy: 'creditsLedger')]
    private Collection $relatedCsId;

    public function __construct()
    {
        $this->relatedCsId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): static
    {
        $this->direction = $direction;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
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

    public function getUserId(): ?user
    {
        return $this->userId;
    }

    public function setUserId(?user $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return Collection<int, carSharings>
     */
    public function getRelatedCsId(): Collection
    {
        return $this->relatedCsId;
    }

    public function addRelatedCsId(carSharings $relatedCsId): static
    {
        if (!$this->relatedCsId->contains($relatedCsId)) {
            $this->relatedCsId->add($relatedCsId);
            $relatedCsId->setCreditsLedger($this);
        }

        return $this;
    }

    public function removeRelatedCsId(carSharings $relatedCsId): static
    {
        if ($this->relatedCsId->removeElement($relatedCsId)) {
            if ($relatedCsId->getCreditsLedger() === $this) {
                $relatedCsId->setCreditsLedger(null);
            }
        }

        return $this;
    }
}
