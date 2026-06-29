<?php

namespace App\Entity;

use App\Repository\EquipmentAssetRepository;
use App\Enum\EquipmentStockStatus;
use App\Enum\ConditionEquipmentStock;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipmentAssetRepository::class)]
#[ORM\HasLifecycleCallbacks]
class EquipmentAsset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // =========================
    // IDENTIFICATION
    // assetTag = ID interne (scan QR code)
    // serialNumber = constructeur
    // =========================
    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    private ?string $serialNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $assetTag = null;

    // =========================
    // MODELE / ENTREPRISE
    // =========================
    #[ORM\ManyToOne(inversedBy: 'equipmentAsset')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    // =========================
    // STATUT STOCK
    // =========================
    #[ORM\Column(enumType: EquipmentStockStatus::class)]
    private EquipmentStockStatus $status;

    // =========================
    // AFFECTATION
    // qui a le matériel ? depuis quand ?
    // =========================
    #[ORM\ManyToOne(inversedBy: 'equipmentAssets')]
    private ?User $assignedTo = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $assignedAt = null;

    // =========================
    // LOCALISATION
    // location : où est utilisé le matériel
    // storageLocation : où il est stocké
    // =========================
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $storageLocation = null;

    // =========================
    // ACHAT / FINANCE
    // =========================
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $purchaseDate = null;

    #[ORM\Column(nullable: true)]
    private ?float $purchasePrice = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $supplier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $invoiceNumber = null;

    // =========================
    // GARANTIE / MAINTENANCE
    // =========================
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $warrantyEndDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastMaintenanceAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maintenanceComment = null;

    // =========================
    // CYCLE DE VIE
    // =========================
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $retiredAt = null;

    // =========================
    // TRACKING / AUDIT
    // =========================
    #[ORM\ManyToOne(inversedBy: 'equipmentAssets')]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'equipmentAssets')]
    private ?User $updatedBy = null;

    // =========================
    // INFORMATIONS LIBRES
    // =========================
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    private static ?User $currentUser = null;

    #[ORM\Column(enumType: ConditionEquipmentStock::class)]
    private ConditionEquipmentStock $assetCondition;

    /**
     * @var Collection<int, TicketEquipment>
     */
    #[ORM\OneToMany(mappedBy: 'equipmentAsset', targetEntity: TicketEquipment::class)]
    private Collection $ticketEquipment;

    public function __construct()
    {
        $this->ticketEquipment = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onCreate(): void
    {
        $now = new \DateTimeImmutable();

        $this->createdAt ??= $now;
        $this->updatedAt = $now;

        $user = self::$currentUser;

        if ($user) {
            $this->createdBy ??= $user;
            $this->updatedBy = $user;
        }

        // sécurité métier
        $this->normalizeState();
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();

        $user = self::$currentUser;

        if ($user) {
            $this->updatedBy = $user;
        }

        $this->normalizeState();
    }

    public function normalizeState(): void
    {
        // =========================
        // STATUS DEFAULT SAFE
        // =========================
        if (!isset($this->status)) {
            $this->status = EquipmentStockStatus::AVAILABLE;
        }

        // =========================
        // ASSIGNMENT CONSISTENCY
        // =========================
        if ($this->assignedTo === null) {
            $this->assignedAt = null;

            if ($this->status === EquipmentStockStatus::ASSIGNED) {
                $this->status = EquipmentStockStatus::AVAILABLE;
            }
        }

        // =========================
        // RETIREMENT CONSISTENCY
        // =========================
        if ($this->status !== EquipmentStockStatus::RETIRED) {
            $this->retiredAt = null;
        }

        // =========================
        // ASSIGNED SAFETY
        // =========================
        if ($this->status === EquipmentStockStatus::ASSIGNED && $this->assignedTo && $this->assignedAt === null) {
            $this->assignedAt = new \DateTimeImmutable();
        }

        // =========================
        // CONDITION FALLBACK (sécurité)
        // =========================
        if (!isset($this->assetCondition)) {
            $this->assetCondition = ConditionEquipmentStock::GOOD;
        }
    }

    public function assignTo(?User $user): static
    {
        $this->assignedTo = $user;

        /*
        if ($user) {
            $this->assignedAt ??= new \DateTimeImmutable();
            $this->status = EquipmentStockStatus::ASSIGNED;
        } else {
            $this->assignedAt = null;
            $this->status = EquipmentStockStatus::AVAILABLE;
        }
        */

        return $this;
    }

    public function reserve(): static
    {
        if ($this->status === EquipmentStockStatus::RETIRED) {
            return $this;
        }

        $this->status = EquipmentStockStatus::RESERVED;

        return $this;
    }

    public function sendToRepair(): static
    {
        if ($this->status === EquipmentStockStatus::RETIRED) {
            return $this;
        }

        $this->status = EquipmentStockStatus::IN_REPAIR;

        return $this;
    }

    public function retire(): static
    {
        $this->status = EquipmentStockStatus::RETIRED;

        $this->retiredAt ??= new \DateTimeImmutable();

        // nettoyage métier
        $this->assignedTo = null;
        $this->assignedAt = null;

        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->status === EquipmentStockStatus::AVAILABLE;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(string $serialNumber): static
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

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
            $ticketEquipment->setEquipmentAsset($this);
        }

        return $this;
    }

    public function removeTicketEquipment(TicketEquipment $ticketEquipment): static
    {
        if ($this->ticketEquipment->removeElement($ticketEquipment)) {
            // set the owning side to null (unless already changed)
            if ($ticketEquipment->getEquipmentAsset() === $this) {
                $ticketEquipment->setEquipmentAsset(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?EquipmentStockStatus
    {
        return $this->status;
    }

    public function setStatus(EquipmentStockStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAssetTag(): ?string
    {
        return $this->assetTag;
    }

    public function setAssetTag(string $assetTag): static
    {
        $this->assetTag = $assetTag;

        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): static
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    public function getAssignedAt(): ?\DateTimeImmutable
    {
        return $this->assignedAt;
    }

    public function setAssignedAt(?\DateTimeImmutable $assignedAt): static
    {
        $this->assignedAt = $assignedAt;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getStorageLocation(): ?string
    {
        return $this->storageLocation;
    }

    public function setStorageLocation(?string $storageLocation): static
    {
        $this->storageLocation = $storageLocation;

        return $this;
    }

    public function getPurchaseDate(): ?\DateTimeImmutable
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(?\DateTimeImmutable $purchaseDate): static
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    public function getPurchasePrice(): ?float
    {
        return $this->purchasePrice;
    }

    public function setPurchasePrice(?float $purchasePrice): static
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    public function getSupplier(): ?string
    {
        return $this->supplier;
    }

    public function setSupplier(?string $supplier): static
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(?string $invoiceNumber): static
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function getWarrantyEndDate(): ?\DateTimeImmutable
    {
        return $this->warrantyEndDate;
    }

    public function setWarrantyEndDate(?\DateTimeImmutable $warrantyEndDate): static
    {
        $this->warrantyEndDate = $warrantyEndDate;

        return $this;
    }

    public function getLastMaintenanceAt(): ?\DateTimeImmutable
    {
        return $this->lastMaintenanceAt;
    }

    public function setLastMaintenanceAt(?\DateTimeImmutable $lastMaintenanceAt): static
    {
        $this->lastMaintenanceAt = $lastMaintenanceAt;

        return $this;
    }

    public function getMaintenanceComment(): ?string
    {
        return $this->maintenanceComment;
    }

    public function setMaintenanceComment(?string $maintenanceComment): static
    {
        $this->maintenanceComment = $maintenanceComment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getRetiredAt(): ?\DateTimeImmutable
    {
        return $this->retiredAt;
    }

    public function setRetiredAt(?\DateTimeImmutable $retiredAt): static
    {
        $this->retiredAt = $retiredAt;

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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getAssetCondition(): ?ConditionEquipmentStock
    {
        return $this->assetCondition;
    }

    public function setAssetCondition(ConditionEquipmentStock $condition): static
    {
        $this->assetCondition = $condition;
        return $this;
    }

    public static function setCurrentUser(?User $user): void
    {
        self::$currentUser = $user;
    }

    public static function getCurrentUser(): ?User
    {
        return self::$currentUser;
    }
}
