<?php

namespace App\UseCase\Ticket;

use App\Entity\TicketEquipment;
use App\Enum\EquipmentStockStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class DeleteTicketEquipmentRowUseCase
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function execute(TicketEquipment $ticketEquipment): array
    {
        $asset = $ticketEquipment->getEquipmentAsset();

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');
        $isManager = $this->security->isGranted('ROLE_MANAGER');
        $isSimpleUser = !$isAdmin && !$isManager;

        $stockUpdated = false;

        // =========================
        // RÈGLE ACCÈS SIMPLE USER
        // =========================
        if ($isSimpleUser && $asset) {
            return [
                'success' => false,
                'status' => 403,
                'message' => 'This equipment is linked to an asset and cannot be deleted. Please contact IT support.',
                'stockUpdated' => false
            ];
        }

        // =========================
        // UPDATE STOCK (ADMIN / MANAGER)
        // =========================
        if (($isAdmin || $isManager) && $asset) {

            if ($asset->getStatus() !== EquipmentStockStatus::AVAILABLE) {
                $asset->setStatus(EquipmentStockStatus::AVAILABLE);
                $this->em->persist($asset);

                $stockUpdated = true;
            }
        }

        $this->em->remove($ticketEquipment);
        $this->em->flush();

        return [
            'success' => true,
            'status' => 200,
            'message' => 'Equipment successfully removed',
            'stockUpdated' => $stockUpdated
        ];
    }
}