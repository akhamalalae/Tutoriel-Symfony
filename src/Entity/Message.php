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
    const ADDED_SUCCESSFULLY = 'ADDED_SUCCESSFULLY';
    const INVALID_FORM = 'INVALID_FORM';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateModification = null;

    #[ORM\ManyToOne(inversedBy: 'messagesCreatorUser', fetch: 'EAGER')]
    private ?User $creatorUser = null;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: DiscussionMessageUser::class)]
    private Collection $discussionMessageUsers;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: FileMessage::class)]
    private Collection $fileMessages;

    private ?string $sensitiveDataMessage = null;

    /**
     * @var Collection<int, DiscussionMessageUser>
     */
    #[ORM\OneToMany(mappedBy: 'answerTo', targetEntity: DiscussionMessageUser::class)]
    private Collection $discussionMessageUsersAnswerTo;

    /**
     * @var Collection<int, AnswerMessage>
     */
    #[ORM\OneToMany(mappedBy: 'message', targetEntity: AnswerMessage::class)]
    private Collection $answerMessages;

    #[ORM\Column]
    private ?bool $isRead = null;

    public function __construct()
    {
        $this->discussionMessageUsers = new ArrayCollection();
        $this->fileMessages = new ArrayCollection();
        $this->discussionMessageUsersAnswerTo = new ArrayCollection();
        $this->answerMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function setSensitiveDataMessage($sensitiveDataMessage): static
    {
        $this->sensitiveDataMessage = $sensitiveDataMessage;

        return $this;
    }

    public function getSensitiveDataMessage(): ?string
    {
        return $this->sensitiveDataMessage;
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

    /**
     * @return Collection<int, FileMessage>
     */
    public function getFileMessages(): Collection
    {
        return $this->fileMessages;
    }

    public function addFileMessage(FileMessage $fileMessage): static
    {
        if (!$this->fileMessages->contains($fileMessage)) {
            $this->fileMessages->add($fileMessage);
            $fileMessage->setMessage($this);
        }

        return $this;
    }

    public function removeFileMessage(FileMessage $fileMessage): static
    {
        if ($this->fileMessages->removeElement($fileMessage)) {
            // set the owning side to null (unless already changed)
            if ($fileMessage->getMessage() === $this) {
                $fileMessage->setMessage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DiscussionMessageUser>
     */
    public function getDiscussionMessageUsersAnswerTo(): Collection
    {
        return $this->discussionMessageUsersAnswerTo;
    }

    public function addDiscussionMessageUsersAnswerTo(DiscussionMessageUser $discussionMessageUsersAnswerTo): static
    {
        if (!$this->discussionMessageUsersAnswerTo->contains($discussionMessageUsersAnswerTo)) {
            $this->discussionMessageUsersAnswerTo->add($discussionMessageUsersAnswerTo);
            $discussionMessageUsersAnswerTo->setAnswerTo($this);
        }

        return $this;
    }

    public function removeDiscussionMessageUsersAnswerTo(DiscussionMessageUser $discussionMessageUsersAnswerTo): static
    {
        if ($this->discussionMessageUsersAnswerTo->removeElement($discussionMessageUsersAnswerTo)) {
            // set the owning side to null (unless already changed)
            if ($discussionMessageUsersAnswerTo->getAnswerTo() === $this) {
                $discussionMessageUsersAnswerTo->setAnswerTo(null);
            }
        }

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
            $answerMessage->setMessage($this);
        }

        return $this;
    }

    public function removeAnswerMessage(AnswerMessage $answerMessage): static
    {
        if ($this->answerMessages->removeElement($answerMessage)) {
            // set the owning side to null (unless already changed)
            if ($answerMessage->getMessage() === $this) {
                $answerMessage->setMessage(null);
            }
        }

        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }
}
