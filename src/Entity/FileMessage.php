<?php

namespace App\Entity;

use App\Repository\FileMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: FileMessageRepository::class)]
class FileMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'fileMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Message $message = null;

    #[ORM\ManyToOne(inversedBy: 'fileMessages')]
    private ?User $creatorUser = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateModification = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mimeType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $originalName = null;

    private ?string $sensitiveDataOriginalName = null;

    private ?string $sensitiveDataName = null;

    private ?string $sensitiveDataMimeType = null;

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

    public function setSensitiveDataMimeType($sensitiveDataMimeType): static
    {
        $this->sensitiveDataMimeType = $sensitiveDataMimeType;

        return $this;
    }

    public function getSensitiveDataMimeType(): ?string
    {
        return $this->sensitiveDataMimeType;
    }

    public function setSensitiveDataOriginalName($sensitiveDataOriginalName): static
    {
        $this->sensitiveDataOriginalName = $sensitiveDataOriginalName;

        return $this;
    }

    public function getSensitiveDataOriginalName(): ?string
    {
        return $this->sensitiveDataOriginalName;
    }

    public function setSensitiveDataName($sensitiveDataName): static
    {
        $this->sensitiveDataName = $sensitiveDataName;

        return $this;
    }

    public function getSensitiveDataName(): ?string
    {
        return $this->sensitiveDataName;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): static
    {
        $this->message = $message;

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

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTimeInterface $dateModification): static
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): static
    {
        $this->originalName = $originalName;

        return $this;
    }
}
