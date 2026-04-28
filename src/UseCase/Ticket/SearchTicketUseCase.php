<?php

namespace App\UseCase\Ticket;

use App\Search\Ticket\TicketSearch;

class SearchTicketUseCase
{
    public function __construct(private TicketSearch $search) {}

    public function execute(string $title, string $description, string $status, int $page): array
    {
        return $this->search->search(
            $title,
            $description,
            $status,
            (int) $page,
        );
    }
}