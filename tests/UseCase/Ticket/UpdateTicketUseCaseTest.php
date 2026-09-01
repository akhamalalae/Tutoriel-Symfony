<?php

namespace App\Tests\UseCase\Ticket;

use App\Entity\Ticket;
use App\Services\Ticket\TicketManager;
use App\UseCase\Ticket\UpdateTicketUseCase;
use App\UseCase\Ticket\ValidatorTicketUseCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UpdateTicketUseCaseTest extends TestCase
{
    public function testExecuteValidatesUpdatesAndFlushes(): void
    {
        $ticket = new Ticket('Title', 'Description');
        $data = ['title' => 'Updated title', 'message' => 'Update note'];

        $em = $this->createMock(EntityManagerInterface::class);
        $manager = $this->createMock(TicketManager::class);
        $validator = $this->createMock(ValidatorTicketUseCase::class);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($data, $ticket);

        $manager
            ->expects($this->once())
            ->method('handle')
            ->with($ticket, $data, true);

        $em->expects($this->once())->method('flush');

        $useCase = new UpdateTicketUseCase($em, $manager, $validator);

        $this->assertSame($ticket, $useCase->execute($ticket, $data));
    }
}
