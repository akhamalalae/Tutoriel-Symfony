<?php

namespace App\UseCase\Ticket;

use App\Entity\Ticket;
use App\Services\User\UserService;
use App\UseCase\Ticket\ValidatorTicketUseCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\Ticket\TicketManager;

class UpdateTicketUseCase
{
    public function __construct(
        private EntityManagerInterface $em,
        private TicketManager $manager,
        private ValidatorTicketUseCase $validator
    ) {}

    public function execute(Ticket $ticket, array $data): Ticket
    {
        $this->validator->validate($data, $ticket);

        $this->manager->handle($ticket, $data, true);

        $this->em->flush();

        return $ticket;
    }
}