<?php

namespace App\Tests\Service\Ticket;

use App\Entity\Ticket;
use App\Services\History\CollectionDiff;
use App\Services\History\EntityChangeDetector;
use App\Services\History\HistoryLogger;
use App\Services\History\HistoryMessageBuilder;
use App\Services\Ticket\MessageManager;
use App\Services\Ticket\TicketHistoryManager;
use PHPUnit\Framework\TestCase;

class TicketHistoryManagerTest extends TestCase
{
    public function testHandleDoesNothingWhenNoChanges(): void
    {
        $ticket = new Ticket('Title', 'Description');
        $original = clone $ticket;

        $detector = $this->createMock(EntityChangeDetector::class);
        $collectionDiff = $this->createMock(CollectionDiff::class);
        $logger = $this->createMock(HistoryLogger::class);
        $builder = $this->createMock(HistoryMessageBuilder::class);
        $messageManager = $this->createMock(MessageManager::class);

        $detector->method('detectChanges')->willReturn([]);
        $collectionDiff->method('detectEquipmentChanges')->willReturn([]);
        $collectionDiff->method('detectSoftwareChanges')->willReturn([]);

        $logger->expects($this->never())->method('log');
        $messageManager->expects($this->never())->method('handle');

        $manager = new TicketHistoryManager(
            $detector,
            $collectionDiff,
            $logger,
            $builder,
            $messageManager
        );

        $manager->handle($ticket, $original, [], []);
    }

    public function testHandleLogsChangesAndCreatesHistoryMessage(): void
    {
        $ticket = new Ticket('New title', 'Description');
        $original = new Ticket('Old title', 'Description');

        $changes = [
            'title' => ['old' => 'Old title', 'new' => 'New title'],
        ];

        $detector = $this->createMock(EntityChangeDetector::class);
        $collectionDiff = $this->createMock(CollectionDiff::class);
        $logger = $this->createMock(HistoryLogger::class);
        $builder = $this->createMock(HistoryMessageBuilder::class);
        $messageManager = $this->createMock(MessageManager::class);

        $detector->method('detectChanges')->willReturn($changes);
        $collectionDiff->method('detectEquipmentChanges')->willReturn([]);
        $collectionDiff->method('detectSoftwareChanges')->willReturn([]);
        $builder->method('build')->with($changes)->willReturn('Title changed');

        $logger
            ->expects($this->once())
            ->method('log')
            ->with($ticket, 'update', 'title', 'Old title', 'New title');

        $messageManager
            ->expects($this->once())
            ->method('handle')
            ->with($ticket, 'Title changed');

        $manager = new TicketHistoryManager(
            $detector,
            $collectionDiff,
            $logger,
            $builder,
            $messageManager
        );

        $manager->handle($ticket, $original, [], []);
    }
}
