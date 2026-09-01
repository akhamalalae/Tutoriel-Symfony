<?php

namespace App\Services\Ticket;

use App\Entity\Ticket;
use App\Services\History\CollectionDiff;
use App\Services\History\EntityChangeDetector;
use App\Services\History\HistoryLogger;
use App\Services\History\HistoryMessageBuilder;

class TicketHistoryManager
{
    public function __construct(
        private EntityChangeDetector $detector,
        private CollectionDiff $collectionDiff,
        private HistoryLogger $logger,
        private HistoryMessageBuilder $builder,
        private MessageManager $messageManager
    ) {}

    public function handle(
        Ticket $ticket,
        Ticket $original,
        array $originalEquipment,
        array $originalSoftware
    ): void {
        $changes = array_merge(
            $this->detector->detectChanges($original, $ticket),
            $this->collectionDiff->detectEquipmentChanges(
                $originalEquipment,
                $ticket->getTicketEquipment()->toArray()
            ),
            $this->collectionDiff->detectSoftwareChanges(
                $originalSoftware,
                $ticket->getTicketSoftware()->toArray()
            )
        );

        if ($changes === []) {
            return;
        }

        foreach ($changes as $field => $change) {
            $this->logger->log(
                $ticket,
                'update',
                $field,
                $change['old'],
                $change['new']
            );
        }

        $message = $this->builder->build($changes);

        if ($message) {
            $this->messageManager->handle($ticket, $message);
        }
    }
}
