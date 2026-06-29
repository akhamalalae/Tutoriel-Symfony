<?php

namespace App\Services\Ticket;

use App\Entity\Ticket;
use App\Entity\Onboarding;
use App\Entity\Offboarding;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use App\Services\User\UserService;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

class TicketUpdater
{
    public function __construct(
        private CompanyRepository $companyRepo,
        private UserRepository $userRepo,
        private UserService $userService,
        private Security $security,
        private EntityManagerInterface $em
    ) {}

    public function update(Ticket $ticket, array $data, bool $isUpdate): void
    {
        $ticket
            ->setTitle($data['title'] ?? $ticket->getTitle())
            ->setDescription($data['description'] ?? $ticket->getDescription())
            ->setType($data['type'] ?? $ticket->getType())
            ->setPriority($data['priority'] ?? $ticket->getPriority());

        if (!empty($data['company'])) {
            $ticket->setCompany($this->companyRepo->find($data['company']));
        }

        if (!empty($data['targetUser'])) {
            $ticket->setTargetUser($this->userRepo->find($data['targetUser']));
        }

        if (!$isUpdate) {
            $ticket
                ->setCreatedBy($this->userService->getAuthenticatedUser())
                ->setCreatedAt(new \DateTimeImmutable());
        }

        $this->handleManagerFields($ticket, $data);
        $this->handleOnboarding($ticket, $data);
        $this->handleOffboarding($ticket, $data);
    }

    private function handleManagerFields(Ticket $ticket, array $data): void
    {
        if (!$this->isManager()) return;

        if (!empty($data['status'])) {
            $ticket->changeStatus($data['status']);
        }

        if (!empty($data['assignedTo'])) {
            $assigned = $this->userRepo->find($data['assignedTo']);
            if ($assigned) {
                $ticket->assignTo($assigned);
            }
        }
    }

    private function handleOnboarding(Ticket $ticket, array $data): void
    {
        if ($ticket->getType()?->value !== 'onboarding') return;

        $onboarding = $ticket->getOnboarding() ?? new Onboarding();

        $onboarding
            ->setTicket($ticket)
            ->setEmployee($ticket->getTargetUser())
            ->setPosition($data['onboardingPosition'] ?? null)
            ->setStartDate($this->date($data['onboardingStartDate'] ?? null));

        $this->em->persist($onboarding);
    }

    private function handleOffboarding(Ticket $ticket, array $data): void
    {
        if ($ticket->getType()?->value !== 'offboarding') return;

        $offboarding = $ticket->getOffboarding() ?? new Offboarding();

        $offboarding
            ->setTicket($ticket)
            ->setEmployee($ticket->getTargetUser())
            ->setRevokeAccess($data['offboardingRevokeAccess'] ?? false)
            ->setReturnEquipment($data['offboardingReturnEquipment'] ?? false)
            ->setEndDate($this->date($data['offboardingEndDate'] ?? null));

        $this->em->persist($offboarding);
    }

    private function isManager(): bool
    {
        return $this->security->isGranted('ROLE_MANAGER')
            || $this->security->isGranted('ROLE_ADMIN');
    }

    private function date(?string $d): ?\DateTimeImmutable
    {
        return $d ? new \DateTimeImmutable($d) : null;
    }
}