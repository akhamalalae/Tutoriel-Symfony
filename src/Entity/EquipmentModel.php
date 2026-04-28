<?php

namespace App\Entity;

use App\Repository\EquipmentModelsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipmentModelsRepository::class)]
class EquipmentModel 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'equipmentAsset')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    /**
     * @var Collection<int, TicketEquipment>
     */
    #[ORM\OneToMany(mappedBy: 'equipment', targetEntity: TicketEquipment::class)]
    private Collection $ticketEquipment;

    public function __construct()
    {
        $this->ticketEquipment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $ticketEquipment->setEquipment($this);
        }

        return $this;
    }

    public function removeTicketEquipment(TicketEquipment $ticketEquipment): static
    {
        if ($this->ticketEquipment->removeElement($ticketEquipment)) {
            // set the owning side to null (unless already changed)
            if ($ticketEquipment->getEquipment() === $this) {
                $ticketEquipment->setEquipment(null);
            }
        }

        return $this;
    }
}
