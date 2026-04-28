<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\TicketStatus;
use App\Enum\TicketType;
use App\Enum\TicketPriority;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // =======================
    // BASIC
    // =======================
    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    // =======================
    // BUSINESS
    // =======================
    #[ORM\Column(enumType: TicketStatus::class)]
    private TicketStatus $status;

    #[ORM\Column(enumType: TicketType::class)]
    private TicketType $type;

    #[ORM\Column(enumType: TicketPriority::class)]
    private TicketPriority $priority;

    // =======================
    // RELATIONS
    // =======================
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $assignedTo = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'tickets')]
    private ?Company $company = null;

    // =======================
    // DATES
    // =======================
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $resolvedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $closedAt = null;


    #[ORM\OneToOne(mappedBy: 'ticket', cascade: ['persist', 'remove'])]
    private ?Offboarding $offboarding = null;

    #[ORM\OneToOne(mappedBy: 'ticket', cascade: ['persist', 'remove'])]
    private ?Onboarding $onboarding = null;

    /**
     * @var Collection<int, TicketMessage>
     */
    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: TicketMessage::class)]
    #[ORM\OrderBy(["createdAt" => "DESC"])]
    private Collection $ticketMessages;

    /**
     * @var Collection<int, TicketEquipment>
     */
    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: TicketEquipment::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ticketEquipment;

    /**
     * @var Collection<int, TicketSoftware>
     */
    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: TicketSoftware::class)]
    private Collection $ticketSoftware;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?User $targetUser = null;

    // =======================
    // CONSTRUCTOR
    // =======================
    public function __construct(
        string $title = '',
        string $description = '',
    ) {
        $this->title = $title;
        $this->description = $description;

        $this->status = TicketStatus::OPEN;

        $this->createdAt = new \DateTimeImmutable();
        $this->ticketMessages = new ArrayCollection();
        $this->ticketEquipment = new ArrayCollection();
        $this->ticketSoftware = new ArrayCollection();
    }

    // =======================
    // BUSINESS METHODS
    // =======================
    public function assignTo(User $consultant): void
    {
        $this->assignedTo = $consultant;
        $this->status = TicketStatus::IN_PROGRESS;
    }

    public function resolve(): void
    {
        $this->status = TicketStatus::RESOLVED;
        $this->resolvedAt = new \DateTimeImmutable();
    }

    public function close(): void
    {
        if ($this->status !== TicketStatus::RESOLVED) {
            throw new \LogicException('Ticket must be resolved before closing');
        }

        $this->status = TicketStatus::CLOSED;
        $this->closedAt = new \DateTimeImmutable();
    }

    public function changeStatus(TicketStatus|string $status): void
    {
        $statusEnum = $status instanceof TicketStatus
            ? $status
            : TicketStatus::from($status);

        if ($statusEnum === TicketStatus::RESOLVED) {
            $this->resolve();
            return;
        }

        if ($statusEnum === TicketStatus::CLOSED) {
            $this->close();
            return;
        }

        $this->status = $statusEnum;
    }

    // =======================
    // GETTERS / SETTERS
    // =======================
    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string { return $this->description; }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): TicketStatus { return $this->status; }

    public function setStatus(TicketStatus|string $status): static
    {
        if (is_string($status)) {
            $this->status = TicketStatus::from($status);
        } else {
            $this->status = $status;
        }

        return $this;
    }

    public function getType(): TicketType { return $this->type; }

    public function setType(TicketType|string $type): static
    {
        if (is_string($type)) {
            $this->type = TicketType::from($type);
        } else {
            $this->type = $type;
        }

        return $this;
    }

    public function getPriority(): TicketPriority { return $this->priority; }

    public function setPriority(TicketPriority|string $priority): static
    {
        if (is_string($priority)) {
            $this->priority = TicketPriority::from($priority);
        } else {
            $this->priority = $priority;
        }

        return $this;
    }

    public function getCreatedBy(): ?User { return $this->createdBy; }

    public function setCreatedBy(?User $user): static
    {
        $this->createdBy = $user;
        return $this;
    }

    public function getAssignedTo(): ?User { return $this->assignedTo; }

    public function setAssignedTo(?User $user): static
    {
        $this->assignedTo = $user;
        return $this;
    }

    public function getCompany(): ?Company { return $this->company; }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getResolvedAt(): ?\DateTimeImmutable { return $this->resolvedAt; }

    public function getClosedAt(): ?\DateTimeImmutable { return $this->closedAt; }

    public function getOffboarding(): ?Offboarding
    {
        return $this->offboarding;
    }

    public function setOffboarding(Offboarding $offboarding): static
    {
        // set the owning side of the relation if necessary
        if ($offboarding->getTicket() !== $this) {
            $offboarding->setTicket($this);
        }

        $this->offboarding = $offboarding;

        return $this;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setResolvedAt(?\DateTimeImmutable $resolvedAt): static
    {
        $this->resolvedAt = $resolvedAt;

        return $this;
    }

    public function setClosedAt(?\DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getOnboarding(): ?Onboarding
    {
        return $this->onboarding;
    }

    public function setOnboarding(?Onboarding $onboarding): static
    {
        // unset the owning side of the relation if necessary
        if ($onboarding === null && $this->onboarding !== null) {
            $this->onboarding->setTicket(null);
        }

        // set the owning side of the relation if necessary
        if ($onboarding !== null && $onboarding->getTicket() !== $this) {
            $onboarding->setTicket($this);
        }

        $this->onboarding = $onboarding;

        return $this;
    }

    /**
     * @return Collection<int, TicketMessage>
     */
    public function getTicketMessages(): Collection
    {
        return $this->ticketMessages;
    }

    public function addTicketMessage(TicketMessage $ticketMessage): static
    {
        if (!$this->ticketMessages->contains($ticketMessage)) {
            $this->ticketMessages->add($ticketMessage);
            $ticketMessage->setTicket($this);
        }

        return $this;
    }

    public function removeTicketMessage(TicketMessage $ticketMessage): static
    {
        if ($this->ticketMessages->removeElement($ticketMessage)) {
            // set the owning side to null (unless already changed)
            if ($ticketMessage->getTicket() === $this) {
                $ticketMessage->setTicket(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TicketEquipment>
     */
    public function getTicketEquipment(): Collection
    {
        return $this->ticketEquipment;
    }

    public function addTicketEquipment(TicketEquipment $ticketEquipment): static
    {
        if (!$this->ticketEquipment->contains($ticketEquipment)) {
            $this->ticketEquipment->add($ticketEquipment);
            $ticketEquipment->setTicket($this);
        }

        return $this;
    }

    public function removeTicketEquipment(TicketEquipment $ticketEquipment): static
    {
        if ($this->ticketEquipment->removeElement($ticketEquipment)) {
            // set the owning side to null (unless already changed)
            if ($ticketEquipment->getTicket() === $this) {
                $ticketEquipment->setTicket(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TicketSoftware>
     */
    public function getTicketSoftware(): Collection
    {
        return $this->ticketSoftware;
    }

    public function addTicketSoftware(TicketSoftware $ticketSoftware): static
    {
        if (!$this->ticketSoftware->contains($ticketSoftware)) {
            $this->ticketSoftware->add($ticketSoftware);
            $ticketSoftware->setTicket($this);
        }

        return $this;
    }

    public function removeTicketSoftware(TicketSoftware $ticketSoftware): static
    {
        if ($this->ticketSoftware->removeElement($ticketSoftware)) {
            // set the owning side to null (unless already changed)
            if ($ticketSoftware->getTicket() === $this) {
                $ticketSoftware->setTicket(null);
            }
        }

        return $this;
    }

    public function getTargetUser(): ?User
    {
        return $this->targetUser;
    }

    public function setTargetUser(?User $targetUser): static
    {
        $this->targetUser = $targetUser;

        return $this;
    }
}