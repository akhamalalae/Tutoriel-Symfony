<?php

namespace App\Entity;

use App\Repository\TicketEquipmentRepository;
use App\Enum\TicketEquipmentStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketEquipmentRepository::class)]
class TicketEquipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ticketEquipment')]
    private ?Ticket $ticket = null;

    #[ORM\ManyToOne(inversedBy: 'ticketEquipment')]
    private ?EquipmentModel $equipmentModel = null;

    #[ORM\Column(length: 255)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'ticketEquipment')]
    private ?User $createdBy = null;

    #[ORM\Column(enumType: TicketEquipmentStatus::class)]
    private TicketEquipmentStatus $status;

    #[ORM\ManyToOne(inversedBy: 'ticketEquipment')]
    private ?EquipmentAsset $equipmentAsset = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = TicketEquipmentStatus::PENDING;
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

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

    public function getEquipmentModel(): ?EquipmentModel
    {
        return $this->equipmentModel;
    }

    public function setEquipmentModel(?EquipmentModel $equipmentModel): static
    {
        $this->equipmentModel = $equipmentModel;

        return $this;
    }

    public function getStatus(): TicketEquipmentStatus
    {
        return $this->status;
    }

    public function setStatus(TicketEquipmentStatus|string $status): static
    {
        if (is_string($status)) {
            $this->status = TicketEquipmentStatus::from($status);
        } else {
            $this->status = $status;
        }

        return $this;
    }

    public function getEquipmentAsset(): ?EquipmentAsset
    {
        return $this->equipmentAsset;
    }

    public function setEquipmentAsset(?EquipmentAsset $equipmentAsset): static
    {
        $this->equipmentAsset = $equipmentAsset;

        return $this;
    }
}
