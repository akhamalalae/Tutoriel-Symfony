<?php

namespace App\Entity;

use App\Repository\SearchMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SearchMessageRepository::class)]
class SearchMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\Column(length: 255)]
    private ?string $fileName = null;

    #[ORM\Column]
    private ?bool $createdThisMonth = null;

    #[ORM\ManyToOne(inversedBy: 'searchMessages')]
    private ?User $creatorUser = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    private ?string $sensitiveDataDescription = '';

    private ?string $sensitiveDataMessage = '';

    private ?string $sensitiveDataFileName = '';

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

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

    public function getSensitiveDataMessage(): ?string
    {
        return $this->sensitiveDataMessage;
    }

    public function setSensitiveDataMessage(string $sensitiveDataMessage): static
    {
        $this->sensitiveDataMessage = $sensitiveDataMessage;

        return $this;
    }

    public function getSensitiveDataFileName(): ?string
    {
        return $this->sensitiveDataFileName;
    }

    public function setSensitiveDataFileName(string $sensitiveDataFileName): static
    {
        $this->sensitiveDataFileName = $sensitiveDataFileName;

        return $this;
    }
}
