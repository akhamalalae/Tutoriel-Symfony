<?php

namespace App\Tests\Service\Ticket;

use App\Entity\Ticket;
use App\Services\Ticket\EquipmentManager;
use App\Services\Ticket\MessageManager;
use App\Services\Ticket\SoftwareManager;
use App\Services\Ticket\TicketHistoryManager;
use App\Services\Ticket\TicketManager;
use App\Services\Ticket\TicketUpdater;
use PHPUnit\Framework\TestCase;

class TicketManagerTest extends TestCase
{
    public function testHandleUpdateCallsAllManagers(): void
    {
        $ticket = new Ticket('', '');

        $updater = $this->createMock(TicketUpdater::class);
        $equipmentManager = $this->createMock(EquipmentManager::class);
        $softwareManager = $this->createMock(SoftwareManager::class);
        $historyManager = $this->createMock(TicketHistoryManager::class);
        $messageManager = $this->createMock(MessageManager::class);

        $updater
            ->expects($this->once())
            ->method('update')
            ->with($ticket, $this->isType('array'), true);

        $equipmentManager
            ->expects($this->once())
            ->method('handle')
            ->with($ticket, $this->isType('array'));

        $softwareManager
            ->expects($this->once())
            ->method('handle')
            ->with($ticket, $this->isType('array'));

        $messageManager
            ->expects($this->once())
            ->method('handle')
            ->with($ticket, 'Mon message');

        $historyManager
            ->expects($this->once())
            ->method('handle');

        $manager = new TicketManager(
            $updater,
            $equipmentManager,
            $softwareManager,
            $historyManager,
            $messageManager
        );

        $manager->handle($ticket, [
            'message' => 'Mon message'
        ], true);
    }

    public function testHandleWithoutMessageDoesNotCallMessageManager(): void
    {
        $ticket = new Ticket('', '');

        $updater = $this->createMock(TicketUpdater::class);
        $equipmentManager = $this->createMock(EquipmentManager::class);
        $softwareManager = $this->createMock(SoftwareManager::class);
        $historyManager = $this->createMock(TicketHistoryManager::class);
        $messageManager = $this->createMock(MessageManager::class);

        $messageManager
            ->expects($this->never())
            ->method('handle');

        $historyManager
            ->expects($this->once())
            ->method('handle');

        $manager = new TicketManager(
            $updater,
            $equipmentManager,
            $softwareManager,
            $historyManager,
            $messageManager
        );

        $manager->handle($ticket, [
            'message' => ''
        ], true);
    }

    public function testHandleCreateDoesNotCallHistoryManager(): void
    {
        $ticket = new Ticket('', '');

        $updater = $this->createMock(TicketUpdater::class);
        $equipmentManager = $this->createMock(EquipmentManager::class);
        $softwareManager = $this->createMock(SoftwareManager::class);
        $historyManager = $this->createMock(TicketHistoryManager::class);
        $messageManager = $this->createMock(MessageManager::class);

        $historyManager
            ->expects($this->never())
            ->method('handle');

        $manager = new TicketManager(
            $updater,
            $equipmentManager,
            $softwareManager,
            $historyManager,
            $messageManager
        );

        $manager->handle($ticket, [
            'message' => ''
        ], false);
    }
}