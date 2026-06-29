<?php

namespace App\Services\Ticket;

use App\Entity\Ticket;
use App\Services\History\EntityChangeDetector;
use App\Services\History\HistoryLogger;
use App\Services\History\HistoryMessageBuilder;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\User\UserService;
use App\Services\Ticket\MessageManager;

class TicketHistoryManager
{
    public function __construct(
        private EntityChangeDetector $detector,
        private HistoryLogger $logger,
        private HistoryMessageBuilder $builder,
        private EntityManagerInterface $em,
        private UserService $userService,
        private MessageManager $messageManager
    ) {}

    public function handle(Ticket $ticket, Ticket $original): void
    {
        $changes = $this->detector->detectChanges($original, $ticket);

        if (empty($changes)) return;

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