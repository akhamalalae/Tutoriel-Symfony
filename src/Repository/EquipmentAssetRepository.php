<?php

namespace App\Repository;

use App\Entity\EquipmentAsset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EquipmentAsset>
 */
class EquipmentAssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipmentAsset::class);
    }

    public function findByLike(string $q, ?int $company = null, int $limit = 100): array
    {
        if (empty($q)) {
            return [];
        }

        $qb = $this->createQueryBuilder('u');

        $qb->leftJoin(
                \App\Entity\TicketEquipment::class,
                'te',
                'WITH',
                'te.equipmentAsset = u.id'
            )
            ->andWhere(
                $qb->expr()->orX(
                    'u.serialNumber LIKE :q',
                    'u.reference LIKE :q'
                )
            )
            ->andWhere('te.id IS NULL')
            ->setParameter('q', '%' . $q . '%')
            ->setMaxResults($limit)
            ->orderBy('u.reference', 'ASC');

        if ($company !== null) {
            $qb->andWhere('u.company = :company')
            ->setParameter('company', $company);
        }

        return $qb->getQuery()->getResult();
    }

    public function getDashboardStats(): array
    {
        $qb = $this->createQueryBuilder('e');

        return [
            'total' => $qb->select('COUNT(e.id)')->getQuery()->getSingleScalarResult(),

            'available' => $this->countByStatus('available'),
            'reserved' => $this->countByStatus('reserved'),
            'assigned' => $this->countByStatus('assigned'),
            'out' => $this->countByStatus('out_of_service'),

            'locations' => $this->createQueryBuilder('e')
                ->select('e.location, COUNT(e.id) as total')
                ->groupBy('e.location')
                ->getQuery()
                ->getResult(),
        ];
    }

    private function countByStatus(string $status): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
