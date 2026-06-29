<?php

namespace App\Services\Ticket;

use App\Entity\Ticket;
use App\Entity\TicketEquipment;
use App\Entity\EquipmentModel;
use App\Entity\EquipmentAsset;
use App\Enum\EquipmentStockStatus;
use App\Enum\TicketStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Services\User\UserService;

class EquipmentManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private UserService $userService
    ) {}

    public function handle(Ticket $ticket, array $data): void
    {
        $existing = [];
        foreach ($ticket->getTicketEquipment() as $te) {
            $existing[$te->getId()] = $te;
        }

        $submittedIds = [];

        foreach ($data['ticketEquipment'] ?? [] as $eqData) {

            $model = $this->find(EquipmentModel::class, $eqData['equipmentModel'] ?? null);
            if (!$model) continue;

            $id = $eqData['id'] ?? null;

            $ticketEq = $id && isset($existing[$id])
                ? $existing[$id]
                : $this->create($ticket);

            if ($id) $submittedIds[] = $id;

            $ticketEq
                ->setEquipmentModel($model)
                ->setComment($eqData['comment'] ?? '');

            if ($this->isManager()) {
                $asset = $this->find(EquipmentAsset::class, $eqData['equipmentAsset'] ?? null);

                if ($asset && !empty($data['status'])) {
                    $ticketEq->setEquipmentAsset($asset);
                    $asset->setStatus($this->resolveStatus($data['status']));
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

    private function create(Ticket $ticket): TicketEquipment
    {
        $te = (new TicketEquipment())
            ->setTicket($ticket)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setCreatedBy($this->userService->getAuthenticatedUser());

        $this->em->persist($te);
        $ticket->addTicketEquipment($te);

        return $te;
    }

    private function resolveStatus(string $status): EquipmentStockStatus
    {
        return match (TicketStatus::from($status)) {
            TicketStatus::CLOSED => EquipmentStockStatus::ASSIGNED,
            default => EquipmentStockStatus::RESERVED,
        };
    }

    private function isManager(): bool
    {
        return $this->security->isGranted('ROLE_MANAGER')
            || $this->security->isGranted('ROLE_ADMIN');
    }

    private function find(string $class, $id)
    {
        return $id ? $this->em->getRepository($class)->find($id) : null;
    }
}