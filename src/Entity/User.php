<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true, nullable: false)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $job = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $brochureFilename = null;

    #[ORM\OneToMany(mappedBy: 'creatorUser', targetEntity: Message::class)]
    private Collection $messagesCreatorUser;

    #[ORM\OneToMany(mappedBy: 'personOne', targetEntity: Discussion::class)]
    private Collection $discussionsPersonOne;

    #[ORM\OneToMany(mappedBy: 'personTwo', targetEntity: Discussion::class)]
    private Collection $discussionsPersonTwo;

    #[ORM\OneToMany(mappedBy: 'creatorUser', targetEntity: Discussion::class)]
    private Collection $discussionsCreatorUser;

    #[ORM\OneToMany(mappedBy: 'modifierUser', targetEntity: Discussion::class)]
    private Collection $discussionsMdifierUser;

    #[ORM\OneToMany(mappedBy: 'creatorUser', targetEntity: DiscussionMessageUser::class)]
    private Collection $discussionMessageUsers;

    #[ORM\OneToMany(mappedBy: 'creatorUser', targetEntity: FileMessage::class)]
    private Collection $fileMessages;

    #[ORM\OneToMany(mappedBy: 'creatorUser', targetEntity: SearchDiscussion::class)]
    private Collection $searchDiscussions;

    #[ORM\OneToMany(mappedBy: 'creatorUser', targetEntity: SearchMessage::class)]
    private Collection $searchMessages;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mimeType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $postal_code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $twitter = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facebook = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $instagram = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedIn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $skills = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $resetTokenExpiresAt = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->messagesCreatorUser = new ArrayCollection();
        $this->discussionsPersonOne = new ArrayCollection();
        $this->discussionsPersonTwo = new ArrayCollection();
        $this->discussionsCreatorUser = new ArrayCollection();
        $this->discussionsMdifierUser = new ArrayCollection();
        $this->discussionMessageUsers = new ArrayCollection();
        $this->fileMessages = new ArrayCollection();
        $this->searchDiscussions = new ArrayCollection();
        $this->searchMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getBrochureFilename(): ?string
    {
        return $this->brochureFilename;
    }

    public function setBrochureFilename(?string $brochureFilename): static
    {
        $this->brochureFilename = $brochureFilename;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesCreatorUser(): Collection
    {
        return $this->messagesCreatorUser;
    }

    public function addMessagesCreatorUser(Message $messagesCreatorUser): static
    {
        if (!$this->messagesCreatorUser->contains($messagesCreatorUser)) {
            $this->messagesCreatorUser->add($messagesCreatorUser);
            $messagesCreatorUser->setCreatorUser($this);
        }

        return $this;
    }

    public function removeMessagesCreatorUser(Message $messagesCreatorUser): static
    {
        if ($this->messagesCreatorUser->removeElement($messagesCreatorUser)) {
            // set the owning side to null (unless already changed)
            if ($messagesCreatorUser->getCreatorUser() === $this) {
                $messagesCreatorUser->setCreatorUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Discussion>
     */
    public function getDiscussionsPersonOne(): Collection
    {
        return $this->discussionsPersonOne;
    }

    public function addDiscussionsPersonOne(Discussion $discussionsPersonOne): static
    {
        if (!$this->discussionsPersonOne->contains($discussionsPersonOne)) {
            $this->discussionsPersonOne->add($discussionsPersonOne);
            $discussionsPersonOne->setPersonOne($this);
        }

        return $this;
    }

    public function removeDiscussionsPersonOne(Discussion $discussionsPersonOne): static
    {
        if ($this->discussionsPersonOne->removeElement($discussionsPersonOne)) {
            // set the owning side to null (unless already changed)
            if ($discussionsPersonOne->getPersonOne() === $this) {
                $discussionsPersonOne->setPersonOne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Discussion>
     */
    public function getDiscussionsPersonTwo(): Collection
    {
        return $this->discussionsPersonTwo;
    }

    public function addDiscussionsPersonTwo(Discussion $discussionsPersonTwo): static
    {
        if (!$this->discussionsPersonTwo->contains($discussionsPersonTwo)) {
            $this->discussionsPersonTwo->add($discussionsPersonTwo);
            $discussionsPersonTwo->setPersonTwo($this);
        }

        return $this;
    }

    public function removeDiscussionsPersonTwo(Discussion $discussionsPersonTwo): static
    {
        if ($this->discussionsPersonTwo->removeElement($discussionsPersonTwo)) {
            // set the owning side to null (unless already changed)
            if ($discussionsPersonTwo->getPersonTwo() === $this) {
                $discussionsPersonTwo->setPersonTwo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Discussion>
     */
    public function getDiscussionsCreatorUser(): Collection
    {
        return $this->discussionsCreatorUser;
    }

    public function addDiscussionsCreatorUser(Discussion $discussionsCreatorUser): static
    {
        if (!$this->discussionsCreatorUser->contains($discussionsCreatorUser)) {
            $this->discussionsCreatorUser->add($discussionsCreatorUser);
            $discussionsCreatorUser->setCreatorUser($this);
        }

        return $this;
    }

    public function removeDiscussionsCreatorUser(Discussion $discussionsCreatorUser): static
    {
        if ($this->discussionsCreatorUser->removeElement($discussionsCreatorUser)) {
            // set the owning side to null (unless already changed)
            if ($discussionsCreatorUser->getCreatorUser() === $this) {
                $discussionsCreatorUser->setCreatorUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Discussion>
     */
    public function getDiscussionsMdifierUser(): Collection
    {
        return $this->discussionsMdifierUser;
    }

    public function addDiscussionsMdifierUser(Discussion $discussionsMdifierUser): static
    {
        if (!$this->discussionsMdifierUser->contains($discussionsMdifierUser)) {
            $this->discussionsMdifierUser->add($discussionsMdifierUser);
            $discussionsMdifierUser->setModifierUser($this);
        }

        return $this;
    }

    public function removeDiscussionsMdifierUser(Discussion $discussionsMdifierUser): static
    {
        if ($this->discussionsMdifierUser->removeElement($discussionsMdifierUser)) {
            // set the owning side to null (unless already changed)
            if ($discussionsMdifierUser->getModifierUser() === $this) {
                $discussionsMdifierUser->setModifierUser(null);
            }
        }

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
            $discussionMessageUser->setCreatorUser($this);
        }

        return $this;
    }

    public function removeDiscussionMessageUser(DiscussionMessageUser $discussionMessageUser): static
    {
        if ($this->discussionMessageUsers->removeElement($discussionMessageUser)) {
            // set the owning side to null (unless already changed)
            if ($discussionMessageUser->getCreatorUser() === $this) {
                $discussionMessageUser->setCreatorUser(null);
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
            $fileMessage->setCreatorUser($this);
        }

        return $this;
    }

    public function removeFileMessage(FileMessage $fileMessage): static
    {
        if ($this->fileMessages->removeElement($fileMessage)) {
            // set the owning side to null (unless already changed)
            if ($fileMessage->getCreatorUser() === $this) {
                $fileMessage->setCreatorUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SearchDiscussion>
     */
    public function getSearchDiscussions(): Collection
    {
        return $this->searchDiscussions;
    }

    public function addSearchDiscussion(SearchDiscussion $searchDiscussion): static
    {
        if (!$this->searchDiscussions->contains($searchDiscussion)) {
            $this->searchDiscussions->add($searchDiscussion);
            $searchDiscussion->setCreatorUser($this);
        }

        return $this;
    }

    public function removeSearchDiscussion(SearchDiscussion $searchDiscussion): static
    {
        if ($this->searchDiscussions->removeElement($searchDiscussion)) {
            // set the owning side to null (unless already changed)
            if ($searchDiscussion->getCreatorUser() === $this) {
                $searchDiscussion->setCreatorUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SearchMessage>
     */
    public function getSearchMessages(): Collection
    {
        return $this->searchMessages;
    }

    public function addSearchMessage(SearchMessage $searchMessage): static
    {
        if (!$this->searchMessages->contains($searchMessage)) {
            $this->searchMessages->add($searchMessage);
            $searchMessage->setCreatorUser($this);
        }

        return $this;
    }

    public function removeSearchMessage(SearchMessage $searchMessage): static
    {
        if ($this->searchMessages->removeElement($searchMessage)) {
            // set the owning side to null (unless already changed)
            if ($searchMessage->getCreatorUser() === $this) {
                $searchMessage->setCreatorUser(null);
            }
        }

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(?string $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): static
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): static
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): static
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getLinkedIn(): ?string
    {
        return $this->linkedIn;
    }

    public function setLinkedIn(?string $linkedIn): static
    {
        $this->linkedIn = $linkedIn;

        return $this;
    }

    public function getSkills(): ?string
    {
        return $this->skills;
    }

    public function setSkills(?string $skills): static
    {
        $this->skills = $skills;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getResetTokenExpiresAt(): ?\DateTime
    {
        return $this->resetTokenExpiresAt;
    }

    public function setResetTokenExpiresAt(?\DateTime $resetTokenExpiresAt): self
    {
        $this->resetTokenExpiresAt = $resetTokenExpiresAt;

        return $this;
    }
}
