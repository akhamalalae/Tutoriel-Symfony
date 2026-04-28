<?php

namespace App\UseCase\Ticket;

use App\Entity\Ticket;
use App\Entity\TicketEquipment;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;

class GetTicketUseCase
{
    public function __construct(private TicketRepository $repo, private UserRepository $userRepository,) {}

    public function execute(int $id): array
    {
        $ticket = $this->repo->find($id);

        if (!$ticket) {
            throw new \RuntimeException('Ticket not found');
        }

        $assignedTo = $ticket->getAssignedTo();
        $company = $ticket->getCompany();
        $onboarding = $ticket->getOnboarding();
        $offboarding = $ticket->getOffboarding();
        $targetUser = $ticket->getTargetUser();

        $ticketEquipments = array_map(fn($te) => [
            'id' => $te->getId(),
            'idEquipmentModel' => $te->getEquipmentModel()?->getId(),
            'name' => $te->getEquipmentModel()?->getName(),
            'idequipmentAsset' => $te->getEquipmentAsset()?->getId(),
            'nameEquipmentAsset' => $te->getEquipmentAsset()?->getReference(),
            'idTicket' => $te->getTicket()?->getId(),
            'comment' => $te->getComment(),
            'status' => $te->getStatus()->value,
        ], $ticket->getTicketEquipment()->toArray());

        /*
        $ticketSoftware = array_map(fn($ts) => [
            'id' => $ts->getId(),
            'idSoftwareProduct' => $ts->getSoftwareProduct()?->getId(),
            'nameSoftwareProduct' => $ts->getSoftwareProduct()?->getName(),
            'idSoftwareLicense' => $ts->getSoftwareLicense()?->getId(),
            'nameSoftwareLicense' => $ts->getSoftwareLicense()?->getKey(),
            'idTicket' => $ts->getTicket()?->getId(),
            'comment' => $ts->getComment(),
            'status' => $ts->getStatus()->value,
        ], $ticket->getTicketSoftware()->toArray());
        */

        return [
            'id' => $ticket->getId(),
            'title' => $ticket->getTitle(),
            'description' => $ticket->getDescription(),
            'status' => $ticket->getStatus()->value,
            'createdAt' => $ticket->getCreatedAt()?->format('Y-m-d H:i:s'),

            'assignedTo' => $assignedTo?->getId(),
            'targetUser' => $targetUser?->getId(),
            
            'assignedToName' => $assignedTo && $assignedTo->getName()
                ? $assignedTo->getSensitiveDataFirstName() . ' ' . $assignedTo->getSensitiveDataName()
                : null,
            'targetUserName' => $targetUser && $targetUser->getName()
                ? $targetUser->getSensitiveDataFirstName() . ' ' . $targetUser->getSensitiveDataName()
                : null,
            'company' => $company?->getId(),

            'type' => $ticket->getType()->value,
            'priority' => $ticket->getPriority()->value,

            // onboarding
            'onboardingPosition' => $onboarding?->getPosition(),
            'onboardingStartDate' => $onboarding?->getStartDate()?->format('Y-m-d H:i:s'),

            // offboarding
            'offboardingRevokeAccess' => $offboarding?->isRevokeAccess(),
            'offboardingReturnEquipment' => $offboarding?->isReturnEquipment(),
            'offboardingEndDate' => $offboarding?->getEndDate()?->format('Y-m-d H:i:s'),

            // equipments
            'ticketEquipments' => $ticketEquipments,
        ];
    }
}