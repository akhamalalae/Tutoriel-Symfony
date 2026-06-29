<?php

namespace App\UseCase\Ticket;

use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\User\UserService;
use App\UseCase\Ticket\ValidatorTicketUseCase;
use App\Services\Ticket\TicketManager;

class CreateTicketUseCase
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorTicketUseCase $validator,
        private UserService $userService,
        private TicketManager $manager
    ) {}

    public function execute(array $data): Ticket
    {
        $ticket = new Ticket($data['title'], $data['description']);

        $this->validator->validate($data, $ticket);

        $ticket->setCreatedBy($this->userService->getAuthenticatedUser());

        $this->em->persist($ticket);
        $this->manager->handle($ticket, $data, false);
        $this->em->flush();

        return $ticket;
    }
}