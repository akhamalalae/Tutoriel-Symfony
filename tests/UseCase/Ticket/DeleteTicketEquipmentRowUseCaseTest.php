<?php

namespace App\Tests\UseCase\Ticket;

use App\Entity\EquipmentAsset;
use App\Entity\TicketEquipment;
use App\Enum\EquipmentStockStatus;
use App\Repository\TicketEquipmentRepository;
use App\UseCase\Ticket\DeleteTicketEquipmentRowUseCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

class DeleteTicketEquipmentRowUseCaseTest extends TestCase
{
    public function testExecuteReturns404WhenEquipmentNotFound(): void
    {
        $repo = $this->createMock(TicketEquipmentRepository::class);
        $repo->method('find')->with(1)->willReturn(null);

        $useCase = new DeleteTicketEquipmentRowUseCase(
            $repo,
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(Security::class)
        );

        $result = $useCase->execute(1);

        $this->assertFalse($result['success']);
        $this->assertSame(404, $result['status']);
        $this->assertSame('Equipment not found', $result['message']);
    }

    public function testExecuteReturns403ForSimpleUserWithLinkedAsset(): void
    {
        $asset = $this->createMock(EquipmentAsset::class);
        $equipment = new TicketEquipment();
        $equipment->setEquipmentAsset($asset);

        $repo = $this->createMock(TicketEquipmentRepository::class);
        $repo->method('find')->willReturn($equipment);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturn(false);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('remove');

        $useCase = new DeleteTicketEquipmentRowUseCase($repo, $em, $security);
        $result = $useCase->execute(1);

        $this->assertFalse($result['success']);
        $this->assertSame(403, $result['status']);
    }

    public function testExecuteRemovesEquipmentAndUpdatesStockForManager(): void
    {
        $asset = $this->createMock(EquipmentAsset::class);
        $asset->method('getStatus')->willReturn(EquipmentStockStatus::RESERVED);
        $asset->expects($this->once())->method('setStatus')->with(EquipmentStockStatus::AVAILABLE);

        $equipment = new TicketEquipment();
        $equipment->setEquipmentAsset($asset);

        $repo = $this->createMock(TicketEquipmentRepository::class);
        $repo->method('find')->willReturn($equipment);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturnCallback(
            fn (string $role) => $role === 'ROLE_MANAGER'
        );

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('persist')->with($asset);
        $em->expects($this->once())->method('remove')->with($equipment);
        $em->expects($this->once())->method('flush');

        $useCase = new DeleteTicketEquipmentRowUseCase($repo, $em, $security);
        $result = $useCase->execute(1);

        $this->assertTrue($result['success']);
        $this->assertSame(200, $result['status']);
        $this->assertTrue($result['stockUpdated']);
    }
}
