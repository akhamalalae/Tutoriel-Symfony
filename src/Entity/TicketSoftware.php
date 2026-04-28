<?php

namespace App\Entity;

use App\Repository\TicketSoftwareRepository;
use Doctrine\DBAL\Types\Types;
use App\Enum\TicketSoftwareStatus;
use App\Entity\SoftwareProduct;
use App\Entity\SoftwareLicense;
use App\Entity\User;
use App\Entity\Ticket;
use App\Entity\TicketEquipment;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketSoftwareRepository::class)]
class TicketSoftware
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: TicketSoftwareStatus::class)]
    private TicketSoftwareStatus $status;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'ticketEquipment')]
    private ?User $createdBy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'ticketSoftware')]
    private ?Ticket $ticket = null;

    #[ORM\ManyToOne(inversedBy: 'ticketSoftware')]
    private ?SoftwareProduct $softwareProduct = null;

    #[ORM\ManyToOne(inversedBy: 'ticketSoftware')]
    private ?SoftwareLicense $softwareLicense = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = TicketSoftwareStatus::PENDING;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getSoftwareProduct(): ?SoftwareProduct
    {
        return $this->softwareProduct;
    }

    public function setSoftwareProduct(?SoftwareProduct $softwareProduct): static
    {
        $this->softwareProduct = $softwareProduct;

        return $this;
    }

    public function getSoftwareLicense(): ?SoftwareLicense
    {
        return $this->softwareLicense;
    }

    public function setSoftwareLicense(?SoftwareLicense $softwareLicense): static
    {
        $this->softwareLicense = $softwareLicense;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getStatus(): TicketSoftwareStatus
    {
        return $this->status;
    }

    public function setStatus(TicketSoftwareStatus|string $status): static
    {
        if (is_string($status)) {
            $this->status = TicketSoftwareStatus::from($status);
        } else {
            $this->status = $status;
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
