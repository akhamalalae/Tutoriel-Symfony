<?php

namespace App\Services\Ticket;

use App\Entity\Ticket;
use App\Entity\TicketMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\User\UserService;

class MessageManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserService $userService
    ) {}

    public function handle(Ticket $ticket, string $message): void
    {
        if ($message) {
            $this->createMessage($ticket, $message);
        }
    }

    private function createMessage(Ticket $ticket, string $content): void
    {
        $msg = new TicketMessage();

        $msg->setTicket($ticket)
            ->setMessage($content)
            ->setCreatedBy($this->userService->getAuthenticatedUser())
            ->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($msg);
    }
}