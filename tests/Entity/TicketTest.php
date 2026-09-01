<?php

namespace App\Tests\Entity;

use App\Entity\Ticket;
use App\Entity\User;
use App\Enum\TicketStatus;
use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase
{
    public function testResolveSetsStatusAndResolvedAt(): void
    {
        $ticket = new Ticket('Title', 'Description');

        $ticket->resolve();

        $this->assertSame(TicketStatus::RESOLVED, $ticket->getStatus());
        $this->assertNotNull($ticket->getResolvedAt());
    }

    public function testCloseRequiresResolvedStatus(): void
    {
        $ticket = new Ticket('Title', 'Description');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Ticket must be resolved before closing');

        $ticket->close();
    }

    public function testCloseAfterResolveSetsClosedAt(): void
    {
        $ticket = new Ticket('Title', 'Description');
        $ticket->resolve();

        $ticket->close();

        $this->assertSame(TicketStatus::CLOSED, $ticket->getStatus());
        $this->assertNotNull($ticket->getClosedAt());
    }

    public function testChangeStatusToResolved(): void
    {
        $ticket = new Ticket('Title', 'Description');

        $ticket->changeStatus(TicketStatus::RESOLVED);

        $this->assertSame(TicketStatus::RESOLVED, $ticket->getStatus());
        $this->assertNotNull($ticket->getResolvedAt());
    }

    public function testChangeStatusToClosedAfterResolved(): void
    {
        $ticket = new Ticket('Title', 'Description');
        $ticket->changeStatus('resolved');

        $ticket->changeStatus('closed');

        $this->assertSame(TicketStatus::CLOSED, $ticket->getStatus());
        $this->assertNotNull($ticket->getClosedAt());
    }

    public function testAssignToSetsAssignedUser(): void
    {
        $ticket = new Ticket('Title', 'Description');
        $user = new User();

        $ticket->assignTo($user);

        $this->assertSame($user, $ticket->getAssignedTo());
    }
}
