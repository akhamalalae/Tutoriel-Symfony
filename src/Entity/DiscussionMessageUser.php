<?php

namespace App\Entity;

use App\Repository\DiscussionMessageUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscussionMessageUserRepository::class)]
class DiscussionMessageUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'discussionMessageUsers', cascade:["persist"])]
    private ?Message $message = null;

    #[ORM\ManyToOne(inversedBy: 'discussionMessageUsers')]
    private ?Discussion $discussion = null;

    #[ORM\ManyToOne(inversedBy: 'discussionMessageUsers')]
    private ?User $creatorUser = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateModification = null;

    /**
     * @var Collection<int, AnswerMessage>
     */
    #[ORM\OneToMany(mappedBy: 'discussionMessageUser', targetEntity: AnswerMessage::class)]
    private Collection $answerMessages;

    public function __construct()
    {
        $this->answerMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDiscussion(): ?Discussion
    {
        return $this->discussion;
    }

    public function setDiscussion(?Discussion $discussion): static
    {
        $this->discussion = $discussion;

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

    /**
     * @return Collection<int, AnswerMessage>
     */
    public function getAnswerMessages(): Collection
    {
        return $this->answerMessages;
    }

    public function addAnswerMessage(AnswerMessage $answerMessage): static
    {
        if (!$this->answerMessages->contains($answerMessage)) {
            $this->answerMessages->add($answerMessage);
            $answerMessage->setDiscussionMessageUser($this);
        }

        return $this;
    }

    public function removeAnswerMessage(AnswerMessage $answerMessage): static
    {
        if ($this->answerMessages->removeElement($answerMessage)) {
            // set the owning side to null (unless already changed)
            if ($answerMessage->getDiscussionMessageUser() === $this) {
                $answerMessage->setDiscussionMessageUser(null);
            }
        }

        return $this;
    }
}
