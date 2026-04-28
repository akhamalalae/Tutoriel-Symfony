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

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $companyUser = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $job = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $brochureFilename = null;

    #[ORM\OneToMany(mappedBy: 'creatorUser', targetEntity: Message::class)]
    private Collection $messagesCreatorUser;

    #[ORM\OneToMany(mappedBy: 'personInvitationSender', targetEntity: Discussion::class)]
    private Collection $discussionsPersonInvitationSender;

    #[ORM\OneToMany(mappedBy: 'personInvitationRecipient', targetEntity: Discussion::class)]
    private Collection $discussionsPersonInvitationRecipient;

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

    private ?string $sensitiveDataName = '';

    private ?string $sensitiveDataFirstName = '';

    private ?string $sensitiveDataFullName = '';

    private ?string $sensitiveDataEmail = '';

    private ?string $sensitiveDataCompany = '';

    private ?string $sensitiveDataJob = '';

    private ?string $sensitiveDataBrochureFilename = '';

    private ?string $sensitiveDataMimeType = '';

    private ?string $sensitiveDataStreet = '';

    private ?string $sensitiveDataCity = '';

    private ?string $sensitiveDataPostalCode = '';

    private ?string $sensitiveDataCountry = '';

    private ?string $sensitiveDataTwitter = '';

    private ?string $sensitiveDataFacebook = '';

    private ?string $sensitiveDataInstagram = '';
    
    private ?string $sensitiveDataLinkedIn = '';

    /**
     * @var Collection<int, AnswerMessage>
     */
    #[ORM\OneToMany(mappedBy: 'creatorUser', targetEntity: AnswerMessage::class)]
    private Collection $answerMessages;

    /**
     * @var Collection<int, Onboarding>
     */
    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Onboarding::class, orphanRemoval: true)]
    private Collection $onboardings;

    /**
     * @var Collection<int, Offboarding>
     */
    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Offboarding::class, orphanRemoval: true)]
    private Collection $offboardings;

    /**
     * @var Collection<int, TicketMessage>
     */
    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: TicketMessage::class, orphanRemoval: true)]
    private Collection $ticketMessagesTicket;

    /**
     * @var Collection<int, TicketEquipment>
     */
    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: TicketEquipment::class)]
    private Collection $ticketEquipment;

    /**
     * @var Collection<int, EquipmentAsset>
     */
    #[ORM\OneToMany(mappedBy: 'assignedTo', targetEntity: EquipmentAsset::class)]
    private Collection $equipmentAssets;

    /**
     * @var Collection<int, SoftwareLicense>
     */
    #[ORM\OneToMany(mappedBy: 'assignedTo', targetEntity: SoftwareLicense::class)]
    private Collection $softwareLicenses;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(mappedBy: 'targetUser', targetEntity: Ticket::class)]
    private Collection $tickets;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->messagesCreatorUser = new ArrayCollection();
        $this->discussionsPersonInvitationSender = new ArrayCollection();
        $this->discussionsPersonInvitationRecipient = new ArrayCollection();
        $this->discussionsCreatorUser = new ArrayCollection();
        $this->discussionsMdifierUser = new ArrayCollection();
        $this->discussionMessageUsers = new ArrayCollection();
        $this->fileMessages = new ArrayCollection();
        $this->searchDiscussions = new ArrayCollection();
        $this->searchMessages = new ArrayCollection();
        $this->answerMessages = new ArrayCollection();
        $this->onboardings = new ArrayCollection();
        $this->offboardings = new ArrayCollection();
        $this->ticketMessagesTicket = new ArrayCollection();
        $this->ticketEquipment = new ArrayCollection();
        $this->equipmentAssets = new ArrayCollection();
        $this->softwareLicenses = new ArrayCollection();
        $this->tickets = new ArrayCollection();
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
    public function getDiscussionsPersonInvitationSender(): Collection
    {
        return $this->discussionsPersonInvitationSender;
    }

    public function addDiscussionsPersonInvitationSender(Discussion $discussionsPersonInvitationSender): static
    {
        if (!$this->discussionsPersonInvitationSender->contains($discussionsPersonInvitationSender)) {
            $this->discussionsPersonInvitationSender->add($discussionsPersonInvitationSender);
            $discussionsPersonInvitationSender->setPersonInvitationSender($this);
        }

        return $this;
    }

    public function removeDiscussionsPersonInvitationSender(Discussion $discussionsPersonInvitationSender): static
    {
        if ($this->discussionsPersonInvitationSender->removeElement($discussionsPersonInvitationSender)) {
            // set the owning side to null (unless already changed)
            if ($discussionsPersonInvitationSender->getPersonInvitationSender() === $this) {
                $discussionsPersonInvitationSender->setPersonInvitationSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Discussion>
     */
    public function getDiscussionsPersonInvitationRecipient(): Collection
    {
        return $this->discussionsPersonInvitationRecipient;
    }

    public function addDiscussionsPersonInvitationRecipient(Discussion $discussionsPersonInvitationRecipient): static
    {
        if (!$this->discussionsPersonInvitationRecipient->contains($discussionsPersonInvitationRecipient)) {
            $this->discussionsPersonInvitationRecipient->add($discussionsPersonInvitationRecipient);
            $discussionsPersonInvitationRecipient->setPersonInvitationRecipient($this);
        }

        return $this;
    }

    public function removeDiscussionsPersonInvitationRecipient(Discussion $discussionsPersonInvitationRecipient): static
    {
        if ($this->discussionsPersonInvitationRecipient->removeElement($discussionsPersonInvitationRecipient)) {
            // set the owning side to null (unless already changed)
            if ($discussionsPersonInvitationRecipient->getPersonInvitationRecipient() === $this) {
                $discussionsPersonInvitationRecipient->setPersonInvitationRecipient(null);
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

    public function getSensitiveDataName(): ?string
    {
        return $this->sensitiveDataName;
    }

    public function setSensitiveDataName(?string $sensitiveDataName): static
    {
        $this->sensitiveDataName = $sensitiveDataName;

        return $this;
    }

    public function getSensitiveDataFirstName(): ?string
    {
        return $this->sensitiveDataFirstName;
    }

    public function setSensitiveDataFirstName(?string $sensitiveDataFirstName): static
    {
        $this->sensitiveDataFirstName = $sensitiveDataFirstName;

        return $this;
    }

    public function getSensitiveDataFullName(): ?string
    {
        return $this->sensitiveDataFirstName . ' ' . $this->sensitiveDataName;
    }

    public function setSensitiveDataFullName(?string $sensitiveDataFullName): static
    {
        $this->sensitiveDataFullName = $sensitiveDataFullName;

        return $this;
    }

    public function getSensitiveDataEmail(): ?string
    {
        return $this->sensitiveDataEmail;
    }

    public function setSensitiveDataEmail(?string $sensitiveDataEmail): static
    {
        $this->sensitiveDataEmail = $sensitiveDataEmail;

        return $this;
    }

    public function getSensitiveDataCompany(): ?string
    {
        return $this->sensitiveDataCompany;
    }

    public function setSensitiveDataCompany(?string $sensitiveDataCompany): static
    {
        $this->sensitiveDataCompany = $sensitiveDataCompany;

        return $this;
    }

    public function getSensitiveDataJob(): ?string
    {
        return $this->sensitiveDataJob;
    }

    public function setSensitiveDataJob(?string $sensitiveDataJob): static
    {
        $this->sensitiveDataJob = $sensitiveDataJob;

        return $this;
    }

    public function getSensitiveDataBrochureFilename(): ?string
    {
        return $this->sensitiveDataBrochureFilename;
    }

    public function setSensitiveDataBrochureFilename(?string $sensitiveDataBrochureFilename): static
    {
        $this->sensitiveDataBrochureFilename = $sensitiveDataBrochureFilename;

        return $this;
    }

    public function getSensitiveDataMimeType(): ?string
    {
        return $this->sensitiveDataMimeType;
    }

    public function setSensitiveDataMimeType(?string $sensitiveDataMimeType): static
    {
        $this->sensitiveDataMimeType = $sensitiveDataMimeType;

        return $this;
    }

    public function getSensitiveDataStreet(): ?string
    {
        return $this->sensitiveDataStreet;
    }

    public function setSensitiveDataStreet(?string $sensitiveDataStreet): static
    {
        $this->sensitiveDataStreet = $sensitiveDataStreet;

        return $this;
    }

    public function getSensitiveDataCity(): ?string
    {
        return $this->sensitiveDataCity;
    }

    public function setSensitiveDataCity(?string $sensitiveDataCity): static
    {
        $this->sensitiveDataCity = $sensitiveDataCity;

        return $this;
    }

    public function getSensitiveDataPostalCode(): ?string
    {
        return $this->sensitiveDataPostalCode;
    }

    public function setSensitiveDataPostalCode(?string $sensitiveDataPostalCode): static
    {
        $this->sensitiveDataPostalCode = $sensitiveDataPostalCode;

        return $this;
    }

    public function getSensitiveDataCountry(): ?string
    {
        return $this->sensitiveDataCountry;
    }

    public function setSensitiveDataCountry(?string $sensitiveDataCountry): static
    {
        $this->sensitiveDataCountry = $sensitiveDataCountry;

        return $this;
    }

    public function getSensitiveDataTwitter(): ?string
    {
        return $this->sensitiveDataTwitter;
    }

    public function setSensitiveDataTwitter(?string $sensitiveDataTwitter): static
    {
        $this->sensitiveDataTwitter = $sensitiveDataTwitter;

        return $this;
    }

    public function getSensitiveDataFacebook(): ?string
    {
        return $this->sensitiveDataFacebook;
    }

    public function setSensitiveDataFacebook(?string $sensitiveDataFacebook): static
    {
        $this->sensitiveDataFacebook = $sensitiveDataFacebook;

        return $this;
    }

    public function getSensitiveDataInstagram(): ?string
    {
        return $this->sensitiveDataInstagram;
    }

    public function setSensitiveDataInstagram(?string $sensitiveDataInstagram): static
    {
        $this->sensitiveDataInstagram = $sensitiveDataInstagram;

        return $this;
    }

    public function getSensitiveDataLinkedIn(): ?string
    {
        return $this->sensitiveDataLinkedIn;
    }

    public function setSensitiveDataLinkedIn(?string $sensitiveDataLinkedIn): static
    {
        $this->sensitiveDataLinkedIn = $sensitiveDataLinkedIn;

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
            $answerMessage->setCreatorUser($this);
        }

        return $this;
    }

    public function removeAnswerMessage(AnswerMessage $answerMessage): static
    {
        if ($this->answerMessages->removeElement($answerMessage)) {
            // set the owning side to null (unless already changed)
            if ($answerMessage->getCreatorUser() === $this) {
                $answerMessage->setCreatorUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Onboarding>
     */
    public function getOnboardings(): Collection
    {
        return $this->onboardings;
    }

    public function addOnboarding(Onboarding $onboarding): static
    {
        if (!$this->onboardings->contains($onboarding)) {
            $this->onboardings->add($onboarding);
            $onboarding->setEmployee($this);
        }

        return $this;
    }

    public function removeOnboarding(Onboarding $onboarding): static
    {
        if ($this->onboardings->removeElement($onboarding)) {
            // set the owning side to null (unless already changed)
            if ($onboarding->getEmployee() === $this) {
                $onboarding->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Offboarding>
     */
    public function getOffboardings(): Collection
    {
        return $this->offboardings;
    }

    public function addOffboarding(Offboarding $offboarding): static
    {
        if (!$this->offboardings->contains($offboarding)) {
            $this->offboardings->add($offboarding);
            $offboarding->setEmployee($this);
        }

        return $this;
    }

    public function removeOffboarding(Offboarding $offboarding): static
    {
        if ($this->offboardings->removeElement($offboarding)) {
            // set the owning side to null (unless already changed)
            if ($offboarding->getEmployee() === $this) {
                $offboarding->setEmployee(null);
            }
        }

        return $this;
    }

    public function getCompanyUser(): ?Company
    {
        return $this->companyUser;
    }

    public function setCompanyUser(?Company $companyUser): static
    {
        $this->companyUser = $companyUser;

        return $this;
    }

    /**
     * @return Collection<int, TicketMessage>
     */
    public function getTicketMessagesTicket(): Collection
    {
        return $this->ticketMessagesTicket;
    }

    public function addTicketMessagesTicket(TicketMessage $ticketMessagesTicket): static
    {
        if (!$this->ticketMessagesTicket->contains($ticketMessagesTicket)) {
            $this->ticketMessagesTicket->add($ticketMessagesTicket);
            $ticketMessagesTicket->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTicketMessagesTicket(TicketMessage $ticketMessagesTicket): static
    {
        if ($this->ticketMessagesTicket->removeElement($ticketMessagesTicket)) {
            // set the owning side to null (unless already changed)
            if ($ticketMessagesTicket->getCreatedBy() === $this) {
                $ticketMessagesTicket->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TicketEquipment>
     */
    public function getTicketEquipment(): Collection
    {
        return $this->ticketEquipment;
    }

    public function addTicketEquipment(TicketEquipment $ticketEquipment): static
    {
        if (!$this->ticketEquipment->contains($ticketEquipment)) {
            $this->ticketEquipment->add($ticketEquipment);
            $ticketEquipment->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTicketEquipment(TicketEquipment $ticketEquipment): static
    {
        if ($this->ticketEquipment->removeElement($ticketEquipment)) {
            // set the owning side to null (unless already changed)
            if ($ticketEquipment->getCreatedBy() === $this) {
                $ticketEquipment->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EquipmentAsset>
     */
    public function getEquipmentAssets(): Collection
    {
        return $this->equipmentAssets;
    }

    public function addEquipmentAsset(EquipmentAsset $equipmentAsset): static
    {
        if (!$this->equipmentAssets->contains($equipmentAsset)) {
            $this->equipmentAssets->add($equipmentAsset);
            $equipmentAsset->setAssignedTo($this);
        }

        return $this;
    }

    public function removeEquipmentAsset(EquipmentAsset $equipmentAsset): static
    {
        if ($this->equipmentAssets->removeElement($equipmentAsset)) {
            // set the owning side to null (unless already changed)
            if ($equipmentAsset->getAssignedTo() === $this) {
                $equipmentAsset->setAssignedTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SoftwareLicense>
     */
    public function getSoftwareLicenses(): Collection
    {
        return $this->softwareLicenses;
    }

    public function addSoftwareLicense(SoftwareLicense $softwareLicense): static
    {
        if (!$this->softwareLicenses->contains($softwareLicense)) {
            $this->softwareLicenses->add($softwareLicense);
            $softwareLicense->setAssignedTo($this);
        }

        return $this;
    }

    public function removeSoftwareLicense(SoftwareLicense $softwareLicense): static
    {
        if ($this->softwareLicenses->removeElement($softwareLicense)) {
            // set the owning side to null (unless already changed)
            if ($softwareLicense->getAssignedTo() === $this) {
                $softwareLicense->setAssignedTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setTargetUser($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getTargetUser() === $this) {
                $ticket->setTargetUser(null);
            }
        }

        return $this;
    }
}
