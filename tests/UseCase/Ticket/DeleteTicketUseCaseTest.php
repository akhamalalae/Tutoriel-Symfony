<?php

namespace App\Tests\UseCase\Ticket;

use App\Entity\Offboarding;
use App\Entity\Onboarding;
use App\Entity\SoftwareLicense;
use App\Entity\Ticket;
use App\Entity\TicketEquipment;
use App\Entity\TicketMessage;
use App\Entity\TicketSoftware;
use App\UseCase\Ticket\DeleteTicketUseCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class DeleteTicketUseCaseTest extends TestCase
{
    public function testExecuteRemovesTicketAndRelatedEntities(): void
    {
        $ticket = new Ticket('Title', 'Description');

        $onboarding = new Onboarding();
        $onboarding->setTicket($ticket);
        $ticket->setOnboarding($onboarding);

        $offboarding = new Offboarding();
        $offboarding->setTicket($ticket);
        $ticket->setOffboarding($offboarding);

        $equipment = new TicketEquipment();
        $equipment->setTicket($ticket);
        $ticket->addTicketEquipment($equipment);

        $license = new SoftwareLicense();
        $software = new TicketSoftware();
        $software->setTicket($ticket);
        $software->setSoftwareLicense($license);
        $ticket->addTicketSoftware($software);

        $message = new TicketMessage();
        $message->setTicket($ticket);
        $ticket->addTicketMessage($message);

        $em = $this->createMock(EntityManagerInterface::class);

        $removed = [];
        $em->method('remove')->willReturnCallback(function ($entity) use (&$removed) {
            $removed[] = $entity;
        });

        $em->expects($this->once())->method('flush');

        $useCase = new DeleteTicketUseCase($em);
        $useCase->execute($ticket);

        $this->assertContains($onboarding, $removed);
        $this->assertContains($offboarding, $removed);
        $this->assertContains($equipment, $removed);
        $this->assertContains($license, $removed);
        $this->assertContains($software, $removed);
        $this->assertContains($message, $removed);
        $this->assertContains($ticket, $removed);
    }
}
