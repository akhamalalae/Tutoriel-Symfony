<?php

namespace App\Entity;

use App\Repository\SoftwareLicenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SoftwareLicenseRepository::class)]
class SoftwareLicense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $licenseKey = null;

    #[ORM\ManyToOne(inversedBy: 'softwareLicenses')]
    private ?SoftwareProduct $softwareProduct = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expiresAt = null;

    /**
     * @var Collection<int, TicketSoftware>
     */
    #[ORM\OneToMany(mappedBy: 'softwareLicense', targetEntity: TicketSoftware::class)]
    private Collection $ticketSoftware;

    public function __construct()
    {
        $this->ticketSoftware = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLicenseKey(): ?string
    {
        return $this->licenseKey;
    }

    public function setLicenseKey(string $licenseKey): static
    {
        $this->licenseKey = $licenseKey;

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

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

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
            $ticketSoftware->setSoftwareLicense($this);
        }

        return $this;
    }

    public function removeTicketSoftware(TicketSoftware $ticketSoftware): static
    {
        if ($this->ticketSoftware->removeElement($ticketSoftware)) {
            // set the owning side to null (unless already changed)
            if ($ticketSoftware->getSoftwareLicense() === $this) {
                $ticketSoftware->setSoftwareLicense(null);
            }
        }

        return $this;
    }
}
