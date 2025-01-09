<?php

namespace App\Entity;

use App\Repository\DiscussionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscussionRepository::class)]
class Discussion
{
    const ADDED_SUCCESSFULLY = 'ADDED_SUCCESSFULLY';
    const INVALID_FORM = 'INVALID_FORM';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'discussionsPersonOne')]
    private ?User $personOne = null;

    #[ORM\ManyToOne(inversedBy: 'discussionsPersonTwo')]
    private ?User $personTwo = null;

    #[ORM\ManyToOne(inversedBy: 'discussionsCreatorUser')]
    private ?User $creatorUser = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateModification = null;

    #[ORM\ManyToOne(inversedBy: 'discussionsMdifierUser')]
    private ?User $modifierUser = null;

    #[ORM\OneToMany(mappedBy: 'discussion', targetEntity: DiscussionMessageUser::class)]
    private Collection $discussionMessageUsers;

    #[ORM\Column(nullable: true)]
    private ?int $personOneNumberUnreadMessages = null;

    #[ORM\Column(nullable: true)]
    private ?int $personTwoNumberUnreadMessages = null;

    public function __construct()
    {
        $this->discussionMessageUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersonOne(): ?User
    {
        return $this->personOne;
    }

    public function setPersonOne(?User $personOne): static
    {
        $this->personOne = $personOne;

        return $this;
    }

    public function getPersonTwo(): ?User
    {
        return $this->personTwo;
    }

    public function setPersonTwo(?User $personTwo): static
    {
        $this->personTwo = $personTwo;

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

    public function getModifierUser(): ?User
    {
        return $this->modifierUser;
    }

    public function setModifierUser(?User $modifierUser): static
    {
        $this->modifierUser = $modifierUser;

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
            $discussionMessageUser->setDiscussion($this);
        }

        return $this;
    }

    public function removeDiscussionMessageUser(DiscussionMessageUser $discussionMessageUser): static
    {
        if ($this->discussionMessageUsers->removeElement($discussionMessageUser)) {
            // set the owning side to null (unless already changed)
            if ($discussionMessageUser->getDiscussion() === $this) {
                $discussionMessageUser->setDiscussion(null);
            }
        }

        return $this;
    }

    public function getPersonOneNumberUnreadMessages(): ?int
    {
        return $this->personOneNumberUnreadMessages;
    }

    public function setPersonOneNumberUnreadMessages(?int $personOneNumberUnreadMessages): static
    {
        $this->personOneNumberUnreadMessages = $personOneNumberUnreadMessages;

        return $this;
    }

    public function getPersonTwoNumberUnreadMessages(): ?int
    {
        return $this->personTwoNumberUnreadMessages;
    }

    public function setPersonTwoNumberUnreadMessages(?int $personTwoNumberUnreadMessages): static
    {
        $this->personTwoNumberUnreadMessages = $personTwoNumberUnreadMessages;

        return $this;
    }
}
