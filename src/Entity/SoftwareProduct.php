<?php

namespace App\Entity;

use App\Repository\SoftwareProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SoftwareProductRepository::class)]
class SoftwareProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $vendor = null;

    /**
     * @var Collection<int, SoftwareLicense>
     */
    #[ORM\OneToMany(mappedBy: 'softwareProduct', targetEntity: SoftwareLicense::class)]
    private Collection $softwareLicenses;

    /**
     * @var Collection<int, TicketSoftware>
     */
    #[ORM\OneToMany(mappedBy: 'softwareProduct', targetEntity: TicketSoftware::class)]
    private Collection $ticketSoftware;

    public function __construct()
    {
        $this->softwareLicenses = new ArrayCollection();
        $this->ticketSoftware = new ArrayCollection();
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

    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    public function setVendor(?string $vendor): static
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * @return Collection<int, SoftwareLicense>
     */
    public function getSoftwareLicenses(): Collection
    {
        return $this->softwareLicenses;
    }

    public function addSoftwareLicense(SoftwareLicense $softwareLicense): static
    {
        if (!$this->softwareLicenses->contains($softwareLicense)) {
            $this->softwareLicenses->add($softwareLicense);
            $softwareLicense->setSoftwareProduct($this);
        }

        return $this;
    }

    public function removeSoftwareLicense(SoftwareLicense $softwareLicense): static
    {
        if ($this->softwareLicenses->removeElement($softwareLicense)) {
            // set the owning side to null (unless already changed)
            if ($softwareLicense->getSoftwareProduct() === $this) {
                $softwareLicense->setSoftwareProduct(null);
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
            $ticketSoftware->setSoftwareProduct($this);
        }

        return $this;
    }

    public function removeTicketSoftware(TicketSoftware $ticketSoftware): static
    {
        if ($this->ticketSoftware->removeElement($ticketSoftware)) {
            // set the owning side to null (unless already changed)
            if ($ticketSoftware->getSoftwareProduct() === $this) {
                $ticketSoftware->setSoftwareProduct(null);
            }
        }

        return $this;
    }
}
