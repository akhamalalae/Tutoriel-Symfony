<?php

namespace App\Tests\UseCase\Ticket;

use App\Entity\Ticket;
use App\Services\Ticket\TicketManager;
use App\UseCase\Ticket\CreateTicketUseCase;
use App\UseCase\Ticket\ValidatorTicketUseCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CreateTicketUseCaseTest extends TestCase
{
    public function testExecuteCreatesPersistsAndHandlesTicket(): void
    {
        $data = [
            'title' => 'New ticket',
            'description' => 'Description',
            'type' => 'incident',
            'priority' => 'high',
            'company' => 1,
            'targetUser' => 2,
        ];

        $em = $this->createMock(EntityManagerInterface::class);
        $validator = $this->createMock(ValidatorTicketUseCase::class);
        $manager = $this->createMock(TicketManager::class);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($data, $this->isInstanceOf(Ticket::class));

        $em
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(fn (Ticket $ticket) => $ticket->getTitle() === 'New ticket'));

        $manager
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Ticket::class), $data, false);

        $em->expects($this->once())->method('flush');

        $useCase = new CreateTicketUseCase($em, $validator, $manager);
        $ticket = $useCase->execute($data);

        $this->assertSame('New ticket', $ticket->getTitle());
        $this->assertSame('Description', $ticket->getDescription());
    }
}
