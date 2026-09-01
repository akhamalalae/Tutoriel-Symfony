<?php

namespace App\Tests\Service\Ticket;

use App\Entity\Ticket;
use App\Entity\User;
use App\Services\Ticket\MessageManager;
use App\Services\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class MessageManagerTest extends TestCase
{
    public function testHandlePersistsMessageWhenContentProvided(): void
    {
        $ticket = new Ticket('Title', 'Description');
        $user = new User();

        $em = $this->createMock(EntityManagerInterface::class);
        $userService = $this->createMock(UserService::class);

        $userService->method('getAuthenticatedUser')->willReturn($user);

        $em
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($message) use ($ticket, $user) {
                return $message->getTicket() === $ticket
                    && $message->getMessage() === 'Hello'
                    && $message->getCreatedBy() === $user
                    && $message->getCreatedAt() instanceof \DateTimeImmutable;
            }));

        $manager = new MessageManager($em, $userService);
        $manager->handle($ticket, 'Hello');
    }

    public function testHandleDoesNothingWhenMessageIsEmpty(): void
    {
        $ticket = new Ticket('Title', 'Description');

        $em = $this->createMock(EntityManagerInterface::class);
        $userService = $this->createMock(UserService::class);

        $em->expects($this->never())->method('persist');

        $manager = new MessageManager($em, $userService);
        $manager->handle($ticket, '');
    }
}
