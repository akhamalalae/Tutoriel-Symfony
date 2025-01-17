<?php

namespace App\Entity;

use App\Repository\AnswerMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnswerMessageRepository::class)]
class AnswerMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'answerMessages')]
    private ?DiscussionMessageUser $discussionMessageUser = null;

    #[ORM\ManyToOne(inversedBy: 'answerMessages')]
    private ?Message $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'answerMessages')]
    private ?User $creatorUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscussionMessageUser(): ?DiscussionMessageUser
    {
        return $this->discussionMessageUser;
    }

    public function setDiscussionMessageUser(?DiscussionMessageUser $discussionMessageUser): static
    {
        $this->discussionMessageUser = $discussionMessageUser;

        return $this;
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

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
}
