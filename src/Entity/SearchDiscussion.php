<?php

namespace App\Entity;

use App\Repository\SearchDiscussionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SearchDiscussionRepository::class)]
class SearchDiscussion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column]
    private ?bool $createdThisMonth = null;

    #[ORM\ManyToOne(inversedBy: 'searchDiscussions')]
    private ?User $creatorUser = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    private ?string $sensitiveDataDescription = '';

    private ?string $sensitiveDataName = '';

    private ?string $sensitiveDataFirstName = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function isCreatedThisMonth(): ?bool
    {
        return $this->createdThisMonth;
    }

    public function setCreatedThisMonth(bool $createdThisMonth): static
    {
        $this->createdThisMonth = $createdThisMonth;

        return $this;
    }

    public function getCreatorUser(): ?User
    {
        return $this->creatorUser;
    }

    public function setCreatorUser(?User $creatorUser): static
    {
        $this->creatorUser = $creatorUser;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getSensitiveDataDescription(): ?string
    {
        return $this->sensitiveDataDescription;
    }

    public function setSensitiveDataDescription(string $sensitiveDataDescription): static
    {
        $this->sensitiveDataDescription = $sensitiveDataDescription;

        return $this;
    }

    public function getSensitiveDataName(): ?string
    {
        return $this->sensitiveDataName;
    }

    public function setSensitiveDataName(string $sensitiveDataName): static
    {
        $this->sensitiveDataName = $sensitiveDataName;

        return $this;
    }

    public function getSensitiveDataFirstName(): ?string
    {
        return $this->sensitiveDataFirstName;
    }

    public function setSensitiveDataFirstName(string $sensitiveDataFirstName): static
    {
        $this->sensitiveDataFirstName = $sensitiveDataFirstName;

        return $this;
    }
}
