<?php

namespace App\Services\Ticket;

use App\Entity\Ticket;
use App\Entity\TicketSoftware;
use App\Entity\SoftwareProduct;
use App\Entity\SoftwareLicense;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Services\User\UserService;

class SoftwareManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private UserService $userService
    ) {}

    public function handle(Ticket $ticket, array $data): void
    {
        $existing = [];
        foreach ($ticket->getTicketSoftware() as $ts) {
            $existing[$ts->getId()] = $ts;
        }

        $submittedIds = [];

        foreach ($data['ticketSoftware'] ?? [] as $swData) {

            $product = $this->find(SoftwareProduct::class, $swData['softwareProduct'] ?? null);
            if (!$product) continue;

            $id = $swData['id'] ?? null;

            $ts = $id && isset($existing[$id])
                ? $existing[$id]
                : $this->create($ticket);

            if ($id) $submittedIds[] = $id;

            $ts->setSoftwareProduct($product)
               ->setComment($swData['comment'] ?? '');

            if ($this->isManager()) {
                $this->handleLicense($ts, $swData, $product);
            }
        }

        foreach ($existing as $id => $ts) {
            if (!in_array($id, $submittedIds)) {
                if ($license = $ts->getSoftwareLicense()) {
                    $this->em->remove($license);
                }
                $ticket->removeTicketSoftware($ts);
                $this->em->remove($ts);
            }
        }
    }

    private function create(Ticket $ticket): TicketSoftware
    {
        $ts = (new TicketSoftware())
            ->setTicket($ticket)
            ->setCreatedBy($this->userService->getAuthenticatedUser());

        $this->em->persist($ts);
        $ticket->addTicketSoftware($ts);

        return $ts;
    }

    private function handleLicense(TicketSoftware $ts, array $data, SoftwareProduct $product): void
    {
        $licenseData = $data['softwareLicense'] ?? null;

        if (empty($licenseData['licenseKey']) && empty($licenseData['expiresAt'])) {
            if ($ts->getSoftwareLicense()) {
                $this->em->remove($ts->getSoftwareLicense());
                $ts->setSoftwareLicense(null);
            }
            return;
        }

        $license = $ts->getSoftwareLicense() ?? new SoftwareLicense();

        if (!$ts->getSoftwareLicense()) {
            $this->em->persist($license);
            $ts->setSoftwareLicense($license);
            $license->setTicketSoftware($ts);
        }

        $license
            ->setLicenseKey($licenseData['licenseKey'] ?? null)
            ->setExpiresAt(
                !empty($licenseData['expiresAt']) 
                    ? new \DateTimeImmutable($licenseData['expiresAt']) 
                    : null
            )
            ->setSoftwareProduct($product);
    }

    private function isManager(): bool
    {
        return $this->security->isGranted('ROLE_MANAGER')
            || $this->security->isGranted('ROLE_ADMIN');
    }

    private function find(string $class, $id)
    {
        return $id ? $this->em->getRepository($class)->find($id) : null;
    }
}