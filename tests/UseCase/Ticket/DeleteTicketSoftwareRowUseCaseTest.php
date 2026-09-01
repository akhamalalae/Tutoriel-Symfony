<?php

namespace App\Tests\UseCase\Ticket;

use App\Entity\SoftwareLicense;
use App\Entity\TicketSoftware;
use App\Repository\TicketSoftwareRepository;
use App\UseCase\Ticket\DeleteTicketSoftwareRowUseCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

class DeleteTicketSoftwareRowUseCaseTest extends TestCase
{
    public function testExecuteReturns404WhenSoftwareNotFound(): void
    {
        $repo = $this->createMock(TicketSoftwareRepository::class);
        $repo->method('find')->with(1)->willReturn(null);

        $useCase = new DeleteTicketSoftwareRowUseCase(
            $repo,
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(Security::class)
        );

        $result = $useCase->execute(1);

        $this->assertFalse($result['success']);
        $this->assertSame(404, $result['status']);
        $this->assertSame('Software not found', $result['message']);
    }

    public function testExecuteReturns403ForSimpleUserWithLinkedLicense(): void
    {
        $license = new SoftwareLicense();
        $software = new TicketSoftware();
        $software->setSoftwareLicense($license);

        $repo = $this->createMock(TicketSoftwareRepository::class);
        $repo->method('find')->willReturn($software);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturn(false);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('remove');

        $useCase = new DeleteTicketSoftwareRowUseCase($repo, $em, $security);
        $result = $useCase->execute(1);

        $this->assertFalse($result['success']);
        $this->assertSame(403, $result['status']);
        $this->assertFalse($result['licenseRevoked']);
    }

    public function testExecuteRemovesSoftwareAndRevokesLicenseForManager(): void
    {
        $license = new SoftwareLicense();
        $software = new TicketSoftware();
        $software->setSoftwareLicense($license);

        $repo = $this->createMock(TicketSoftwareRepository::class);
        $repo->method('find')->willReturn($software);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturnCallback(
            fn (string $role) => $role === 'ROLE_MANAGER'
        );

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->exactly(2))->method('remove');
        $em->expects($this->once())->method('flush');

        $useCase = new DeleteTicketSoftwareRowUseCase($repo, $em, $security);
        $result = $useCase->execute(1);

        $this->assertTrue($result['success']);
        $this->assertSame(200, $result['status']);
        $this->assertTrue($result['licenseRevoked']);
    }
}
