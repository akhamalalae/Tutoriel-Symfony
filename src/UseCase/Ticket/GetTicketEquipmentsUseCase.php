<?php

namespace App\UseCase\Ticket;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\Collection;

class GetTicketEquipmentsUseCase
{
    public function __construct(private TicketRepository $repo) {}

    public function execute(int $id): Collection
    {
        $ticket = $this->repo->find($id);

        if (!$ticket) {
            throw new \RuntimeException('Ticket not found');
        }

        return $ticket->getTicketEquipment();
    }
}