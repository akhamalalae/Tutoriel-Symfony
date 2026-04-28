<?php

namespace App\UseCase\Ticket;

use App\Entity\TicketSoftware;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class DeleteTicketSoftwareRowUseCase
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function execute(TicketSoftware $ticketSoftware): array
    {
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');
        $isManager = $this->security->isGranted('ROLE_MANAGER');
        $isSimpleUser = !$isAdmin && !$isManager;

        $license = $ticketSoftware->getSoftwareLicense();
        $licenseRevoked = false;
        $hasLicense = $license !== null;

        if ($isSimpleUser && $hasLicense) {
            return [
                'success' => false,
                'status' => 403,
                'licenseRevoked' => $licenseRevoked,
                'message' => 'This software is linked to a license and cannot be deleted. Please contact IT support.',
            ];
        }

        if ($hasLicense) {
            $this->em->remove($license);
            $licenseRevoked = true;
        }

        $this->em->remove($ticketSoftware);
        $this->em->flush();

        return [
            'success' => true,
            'status' => 200,
            'licenseRevoked' => $licenseRevoked,
            'message' => 'Software successfully removed',
        ];
    }
}