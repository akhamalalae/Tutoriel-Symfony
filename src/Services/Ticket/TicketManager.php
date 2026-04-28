<?php

namespace App\Services\Ticket;

use App\Entity\Ticket;
use App\Entity\Onboarding;
use App\Entity\Offboarding;
use App\Entity\TicketMessage;
use App\Entity\EquipmentModel;
use App\Entity\EquipmentAsset;
use App\Entity\SoftwareProduct;
use App\Entity\SoftwareLicense;
use App\Entity\TicketEquipment;
use App\Entity\TicketSoftware;
use App\Enum\TicketStatus;
use App\Enum\EquipmentStockStatus;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use App\Services\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Services\History\EntityChangeDetector;
use App\Services\History\HistoryLogger;
use App\Services\History\CollectionDiff;
use Doctrine\Common\Collections\Collection;
use App\DTO\Ticket\Software;

class TicketManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private CompanyRepository $companyRepo,
        private UserRepository $userRepo,
        private UserService $userService,
        private Security $security,
        private EntityChangeDetector $changeDetector,
        private HistoryLogger $historyLogger,
        private CollectionDiff $diff
    ) {}

    public function handle(Ticket $ticket, array $data, bool $isUpdate): void
    {
        $originalTicket = clone $ticket;

        $this->hydrateCore($ticket, $data);
        $this->handleManagerFields($ticket, $data);

        $this->handleOnboarding($ticket, $data);
        $this->handleOffboarding($ticket, $data);

        $this->handleEquipment($ticket, $data);
        $this->handleSoftware($ticket, $data);

        $this->handleMessages($ticket, $data, $isUpdate);

        $changes = $this->changeDetector->detectChanges($originalTicket, $ticket);

        foreach ($changes as $field => $change) {
            $this->historyLogger->log(
                $ticket,
                'update',
                $field,
                $change['old'],
                $change['new']
            );
        }
    }

    // ================= CORE =================

    private function hydrateCore(Ticket $ticket, array $data): void
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

    // ================= ONBOARDING =================

    private function handleOnboarding(Ticket $ticket, array $data): void
    {
        if ($ticket->getType()?->value !== 'onboarding') return;

        $onboarding = $ticket->getOnboarding() ?? new Onboarding();

        $onboarding
            ->setTicket($ticket)
            ->setEmployee($ticket->getTargetUser())
            ->setPosition($data['onboardingPosition'] ?? null)
            ->setStartDate($this->dateOrNull($data['onboardingStartDate'] ?? null));

        $this->em->persist($onboarding);
    }

    // ================= OFFBOARDING =================

    private function handleOffboarding(Ticket $ticket, array $data): void
    {
        if ($ticket->getType()?->value !== 'offboarding') return;

        $offboarding = $ticket->getOffboarding() ?? new Offboarding();

        $offboarding
            ->setTicket($ticket)
            ->setEmployee($ticket->getTargetUser())
            ->setRevokeAccess($data['offboardingRevokeAccess'] ?? false)
            ->setReturnEquipment($data['offboardingReturnEquipment'] ?? false)
            ->setEndDate($this->dateOrNull($data['offboardingEndDate'] ?? null));

        $this->em->persist($offboarding);
    }

    // ================= EQUIPMENT =================

    private function handleEquipment(Ticket $ticket, array $data): void
    {
        $existing = [];
        foreach ($ticket->getTicketEquipment() as $te) {
            $existing[$te->getId()] = $te;
        }

        $submittedIds = [];

        foreach ($data['ticketEquipment'] ?? [] as $eqData) {

            $model = $this->findOrNull(EquipmentModel::class, $eqData['equipmentModel'] ?? null);
            if (!$model) continue;

            $id = $eqData['id'] ?? null;

            if ($id && isset($existing[$id])) {
                $ticketEq = $existing[$id];
                $submittedIds[] = $id;
            } else {
                $ticketEq = (new TicketEquipment())
                    ->setTicket($ticket)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setCreatedBy($this->userService->getAuthenticatedUser());

                $this->em->persist($ticketEq);
                $ticket->addTicketEquipment($ticketEq);
            }

            $ticketEq
                ->setEquipmentModel($model)
                ->setComment($eqData['comment'] ?? '');

            if ($this->isManager()) {
                $asset = $this->findOrNull(EquipmentAsset::class, $eqData['equipmentAsset'] ?? null);

                if ($asset && !empty($data['status'])) {
                    $ticketEq->setEquipmentAsset($asset);
                    $asset->setStatus($this->resolveAssetStatus($data['status']));
                }
            }
        }

        foreach ($existing as $id => $te) {
            if (!in_array($id, $submittedIds)) {
                if ($asset = $te->getEquipmentAsset()) {
                    $asset->setStatus(EquipmentStockStatus::AVAILABLE);
                }
                $ticket->removeTicketEquipment($te);
            }
        }
    }

    // ================= SOFTWARE =================

    private function handleSoftware(Ticket $ticket, array $data): void
    {
        $existing = [];
        foreach ($ticket->getTicketSoftware() as $ts) {
            $existing[$ts->getId()] = $ts;
        }

        $submittedIds = [];

        foreach ($data['ticketSoftware'] ?? [] as $swData) {

            $product = $this->findOrNull(SoftwareProduct::class, $swData['softwareProduct'] ?? null);
            if (!$product) continue;

            $id = $swData['id'] ?? null;

            if ($id && isset($existing[$id])) {
                $ticketSw = $existing[$id];
                $submittedIds[] = $id;
            } else {
                $ticketSw = (new TicketSoftware())
                    ->setCreatedBy($this->userService->getAuthenticatedUser())
                    ->setTicket($ticket);

                $this->em->persist($ticketSw);
                $ticket->addTicketSoftware($ticketSw);
            }

            $ticketSw
                ->setSoftwareProduct($product)
                ->setComment($swData['comment'] ?? '');

            if ($this->isManager()) {
                $this->handleLicense($ticketSw, $swData, $product);
            }
        }

        foreach ($existing as $id => $ts) {
            if (!in_array($id, $submittedIds)) {
                if ($license = $ts->getSoftwareLicense()) {
                    $this->em->remove($license);
                }
                $ticket->removeTicketSoftware($ts);
                $this->em->remove($ts);
            }
        }
    }

    private function handleLicense(TicketSoftware $ticketSw, array $swData, SoftwareProduct $product): void
    {
        $licenseData = $swData['softwareLicense'] ?? null;

        if (empty($licenseData['licenseKey']) && empty($licenseData['expiresAt'])) {
            if ($ticketSw->getSoftwareLicense()) {
                $this->em->remove($ticketSw->getSoftwareLicense());
                $ticketSw->setSoftwareLicense(null);
            }
            return;
        }

        $license = $ticketSw->getSoftwareLicense() ?? new SoftwareLicense();

        if (!$ticketSw->getSoftwareLicense()) {
            $this->em->persist($license);
            $ticketSw->setSoftwareLicense($license);
        }

        $license->setLicenseKey($licenseData['licenseKey'] ?? null);
        $license->setExpiresAt($this->dateOrNull($licenseData['expiresAt'] ?? null));
        $license->setSoftwareProduct($product);
    }

    // ================= MESSAGE =================

    private function handleMessages(Ticket $ticket, array $data, bool $isUpdate): void
    {
        if (!empty($data['message'])) {
            $this->createMessage($ticket, $data['message']);
        }
    }

    private function createMessage(Ticket $ticket, string $content): void
    {
        $message = new TicketMessage();

        $message
            ->setTicket($ticket)
            ->setMessage($content)
            ->setCreatedBy($this->userService->getAuthenticatedUser())
            ->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($message);
    }

    // ================= UTILS =================

    private function isManager(): bool
    {
        return $this->security->isGranted('ROLE_MANAGER')
            || $this->security->isGranted('ROLE_ADMIN');
    }

    private function findOrNull(string $class, $id)
    {
        return $id ? $this->em->getRepository($class)->find($id) : null;
    }

    private function dateOrNull(?string $date): ?\DateTimeImmutable
    {
        return $date ? new \DateTimeImmutable($date) : null;
    }

    private function resolveAssetStatus(string $status): EquipmentStockStatus
    {
        return match (TicketStatus::from($status)) {
            TicketStatus::CLOSED => EquipmentStockStatus::ASSIGNED,
            default => EquipmentStockStatus::RESERVED,
        };
    }
}