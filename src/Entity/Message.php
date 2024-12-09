<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    const MESSAGE_ADDED_SUCCESSFULLY = 'MESSAGE_ADDED_SUCCESSFULLY';
    const MESSAGE_INVALID_FORM = 'MESSAGE_INVALID_FORM';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateModification = null;

    #[ORM\Column(length: 255)]
    private ?string $objet = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'messagesCreatorUser')]
    private ?User $creatorUser = null;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: DiscussionMessageUser::class)]
    private Collection $discussionMessageUsers;

    public function __construct()
    {
        $this->discussionMessageUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTimeInterface $dateModification): static
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): static
    {
        $this->objet = $objet;

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

    public function getCreatorUser(): ?User
    {
        return $this->creatorUser;
    }

    public function setCreatorUser(?User $creatorUser): static
    {
        $this->creatorUser = $creatorUser;

        return $this;
    }

    /**
     * @return Collection<int, DiscussionMessageUser>
     */
    public function getDiscussionMessageUsers(): Collection
    {
        return $this->discussionMessageUsers;
    }

    public function addDiscussionMessageUser(DiscussionMessageUser $discussionMessageUser): static
    {
        if (!$this->discussionMessageUsers->contains($discussionMessageUser)) {
            $this->discussionMessageUsers->add($discussionMessageUser);
            $discussionMessageUser->setMessage($this);
        }

        return $this;
    }

    public function removeDiscussionMessageUser(DiscussionMessageUser $discussionMessageUser): static
    {
        if ($this->discussionMessageUsers->removeElement($discussionMessageUser)) {
            // set the owning side to null (unless already changed)
            if ($discussionMessageUser->getMessage() === $this) {
                $discussionMessageUser->setMessage(null);
            }
        }

        return $this;
    }
}
