<?php

namespace App\Entity;

use App\Repository\OffboardingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffboardingRepository::class)]
class Offboarding
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'offboarding', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ticket $ticket = null;

    #[ORM\ManyToOne(inversedBy: 'offboardings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $employee = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(nullable: true)]
    private ?bool $revokeAccess = null;

    #[ORM\Column(nullable: true)]
    private ?bool $returnEquipment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(Ticket $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isRevokeAccess(): ?bool
    {
        return $this->revokeAccess;
    }

    public function setRevokeAccess(?bool $revokeAccess): static
    {
        $this->revokeAccess = $revokeAccess;

        return $this;
    }

    public function isReturnEquipment(): ?bool
    {
        return $this->returnEquipment;
    }

    public function setReturnEquipment(?bool $returnEquipment): static
    {
        $this->returnEquipment = $returnEquipment;

        return $this;
    }
}
