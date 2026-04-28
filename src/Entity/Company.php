<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // =======================
    // INFOS
    // =======================
    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    // =======================
    // CONFIG IT (optionnel)
    // =======================
    #[ORM\Column(nullable: true)]
    private ?int $slaResponseTime = null; // en minutes

    #[ORM\Column(nullable: true)]
    private ?int $slaResolutionTime = null; // en minutes

    // =======================
    // RELATIONS
    // =======================
    #[ORM\OneToMany(mappedBy: 'companyUser', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Ticket::class)]
    private Collection $tickets;

    // =======================
    // DATES
    // =======================
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, EquipmentModel>
     */
    #[ORM\OneToMany(mappedBy: 'company', targetEntity: EquipmentModel::class, orphanRemoval: true)]
    private Collection $equipmentModel;

    /**
     * @var Collection<int, EquipmentAsset>
     */
    #[ORM\OneToMany(mappedBy: 'company', targetEntity: EquipmentAsset::class)]
    private Collection $equipmentAsset;

    // =======================
    // CONSTRUCTOR
    // =======================
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->users = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->equipmentModel = new ArrayCollection();
        $this->equipmentAsset = new ArrayCollection();
    }

    // =======================
    // GETTERS / SETTERS
    // =======================
    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name ?? ''; }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?string { return $this->address; }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getCity(): ?string { return $this->city; }

    public function setCity(?string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): ?string { return $this->country; }

    public function setCountry(?string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getPhone(): ?string { return $this->phone; }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string { return $this->email; }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getSlaResponseTime(): ?int { return $this->slaResponseTime; }

    public function setSlaResponseTime(?int $time): static
    {
        $this->slaResponseTime = $time;
        return $this;
    }

    public function getSlaResolutionTime(): ?int { return $this->slaResolutionTime; }

    public function setSlaResolutionTime(?int $time): static
    {
        $this->slaResolutionTime = $time;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    // =======================
    // RELATIONS HELPERS
    // =======================
    public function getUsers(): Collection { return $this->users; }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            if ($user->getCompany() === $this) {
                $user->setCompany(null);
            }
        }

        return $this;
    }

    public function getTickets(): Collection { return $this->tickets; }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setCompany($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            if ($ticket->getCompany() === $this) {
                $ticket->setCompany(null);
            }
        }

        return $this;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, EquipmentModel>
     */
    public function getEquipmentModel(): Collection
    {
        return $this->equipmentModel;
    }

    public function addEquipmentModel(EquipmentModel $equipmentModel): static
    {
        if (!$this->equipmentModel->contains($equipmentModel)) {
            $this->equipmentModel->add($equipmentModel);
            $equipmentModel->setCompany($this);
        }

        return $this;
    }

    public function removeEquipmentModel(EquipmentModel $equipmentModel): static
    {
        if ($this->equipmentModel->removeElement($equipmentModel)) {
            // set the owning side to null (unless already changed)
            if ($equipmentModel->getCompany() === $this) {
                $equipmentModel->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EquipmentAsset>
     */
    public function getEquipmentAsset(): Collection
    {
        return $this->equipmentAsset;
    }

    public function addEquipmentAsset(EquipmentAsset $equipmentAsset): static
    {
        if (!$this->equipmentAsset->contains($equipmentAsset)) {
            $this->equipmentAsset->add($equipmentAsset);
            $equipmentAsset->setCompany($this);
        }

        return $this;
    }

    public function removeEquipmentAsset(EquipmentAsset $equipmentAsset): static
    {
        if ($this->equipmentAsset->removeElement($equipmentAsset)) {
            // set the owning side to null (unless already changed)
            if ($equipmentAsset->getCompany() === $this) {
                $equipmentAsset->setCompany(null);
            }
        }

        return $this;
    }
}