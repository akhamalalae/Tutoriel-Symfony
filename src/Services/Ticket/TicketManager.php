<?php

namespace App\Services\Ticket;

use App\Entity\Ticket;
use App\Services\Ticket\TicketUpdater;
use App\Services\Ticket\EquipmentManager;
use App\Services\Ticket\SoftwareManager;
use App\Services\Ticket\TicketHistoryManager;
use App\Services\Ticket\MessageManager;

class TicketManager
{
    public function __construct(
        private TicketUpdater $updater,
        private EquipmentManager $equipmentManager,
        private SoftwareManager $softwareManager,
        private TicketHistoryManager $historyManager,
        private MessageManager $messageManager
    ) {}

    public function handle(Ticket $ticket, array $data, bool $isUpdate): void
    {
        $original = $isUpdate ? clone $ticket : null;

        $this->updater->update($ticket, $data, $isUpdate);

        $this->equipmentManager->handle($ticket, $data);
        $this->softwareManager->handle($ticket, $data);

        $message = $data['message'];
        
        if ($message) {
            $this->messageManager->handle($ticket, $message);
        }

        if ($isUpdate) {
            $this->historyManager->handle($ticket, $original);
        }
    }
}