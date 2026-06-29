<?php

namespace App\UseCase\Ticket;

use App\Entity\Ticket;
use App\UseCase\Exception\ValidationException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Enum\TicketStatus;
use App\Entity\EquipmentAsset;

class ValidatorTicketUseCase
{
    public function __construct(
        private TranslatorInterface $translator,
        private \Doctrine\ORM\EntityManagerInterface $em
    ){}

    public function validate(array $data, ?Ticket $ticket = null): void
    {
        $errors = [];

        // ======================
        // Champs obligatoires
        // ======================

        if (empty($data['title'])) {
            $errors['title'] = $this->translation('ticket.error.title_required');
        }

        if (empty($data['description'])) {
            $errors['description'] = $this->translation('ticket.error.description_required');
        }

        if (empty($data['type'])) {
            $errors['type'] = $this->translation('ticket.error.type_required');
        }

        if (empty($data['priority'])) {
            $errors['priority'] = $this->translation('ticket.error.priority_required');
        }

        if (empty($data['company'])) {
            $errors['company'] = $this->translation('ticket.error.company_required');
        }

        if (!empty($data['company']) && !empty($data['ticketEquipment'])) {

            $companyId = (int) $data['company'];
        
            $ids = array_filter(array_column($data['ticketEquipment'], 'equipmentAsset'));
        
            $equipmentAssets = $this->em->getRepository(EquipmentAsset::class)
                ->findBy(['id' => $ids]);
        
            foreach ($equipmentAssets as $equipmentAsset) {
                $assetCompany = $equipmentAsset->getCompany();
        
                if ($assetCompany && $assetCompany->getId() !== $companyId) {
                    $errors['company'] = $this->translation('ticket.error.equipment_company_mismatch');
                    break;
                }
            }
        }

        if (empty($data['targetUser'])) {
            $errors['targetUser'] = $this->translation('ticket.error.target_user_required');
        }

        // ======================
        // Onboarding
        // ======================
        if (($data['type'] ?? null) === 'onboarding') {
            if (empty($data['onboardingPosition'])) {
                $errors['onboardingPosition'] = $this->translation('ticket.error.onboarding.position_required');
            }
            if (empty($data['onboardingStartDate'])) {
                $errors['onboardingStartDate'] = $this->translation('ticket.error.onboarding.start_date_required');
            }
        }

        // ======================
        // Offboarding
        // ======================
        if (($data['type'] ?? null) === 'offboarding') {
            if (empty($data['offboardingEndDate'])) {
                $errors['offboardingEndDate'] = $this->translation('ticket.error.offboarding.end_date_required');
            }
        }

        // ======================
        // Equipments
        // ======================
        if (!empty($data['ticketEquipment'])) {
            foreach ($data['ticketEquipment'] as $index => $eq) {
                if (empty($eq['equipmentModel'])) {
                    $errors["ticketEquipment"] = $this->translation('ticket.error.equipment_required');
                    break;
                }
            }
        }

        // ======================
        // Software
        // ======================
        if (!empty($data['ticketSoftware'])) {
            foreach ($data['ticketSoftware'] as $index => $eq) {
                if (empty($eq['softwareProduct'])) {
                    $errors["ticketSoftware"] = $this->translation('ticket.error.software_required');
                }
            }
        }

        // ======================
        // Règles métier
        // ======================
        $newStatus = isset($data['status'])
            ? TicketStatus::from($data['status'])
            : null;

        $currentStatus = $ticket?->getStatus();

        $this->validateStatus($currentStatus, $newStatus, $errors);

        // ======================
        // THROW
        // ======================
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    private function validateStatus(?TicketStatus $current, ?TicketStatus $new, array &$errors): void
    {
        if (!$current || !$new) return;

        if ($current === TicketStatus::CLOSED) {
            $errors['status'] = $this->translation(
                $new === TicketStatus::CLOSED
                    ? 'ticket.error.status_already_closed'
                    : 'ticket.error.status_closed_final'
            );
            return;
        }

        if ($new === TicketStatus::CLOSED && $current !== TicketStatus::RESOLVED) {
            $errors['status'] = $this->translation('ticket.error.status_must_be_resolved');
        }
    }

    private function translation(string $label): string
    {
        return $this->translator->trans($label);
    }
}