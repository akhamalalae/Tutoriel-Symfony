<?php

namespace App\UseCase\Ticket;

use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;

class DeleteTicketUseCase
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function execute(Ticket $ticket): void
    {
        // =========================
        // ONBOARDING / OFFBOARDING
        // =========================
        if ($onboarding = $ticket->getOnboarding()) {
            $this->em->remove($onboarding);
        }

        if ($offboarding = $ticket->getOffboarding()) {
            $this->em->remove($offboarding);
        }

        // =========================
        // EQUIPMENTS
        // =========================
        foreach ($ticket->getTicketEquipment() as $equipment) {
            $this->em->remove($equipment);
        }

        // =========================
        // SOFTWARE
        // =========================
        foreach ($ticket->getTicketSoftware() as $software) {

            if ($license = $software->getSoftwareLicense()) {
                $this->em->remove($license);
            }

            $this->em->remove($software);
        }

        // =========================
        // MESSAGES
        // =========================
        foreach ($ticket->getTicketMessages() as $message) {
            $this->em->remove($message);
        }

        // =========================
        // TICKET
        // =========================
        $this->em->remove($ticket);
        $this->em->flush();
    }
}