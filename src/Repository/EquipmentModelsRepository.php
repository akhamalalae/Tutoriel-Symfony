<?php

namespace App\Repository;

use App\Entity\EquipmentModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EquipmentModel>
 */
class EquipmentModelsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipmentModel::class);
    }

    public function findByNameLike(string $q, ?int $company = null, int $limit = 10): array
    {
        if (empty($q)) {
            return [];
        }

        return $this->createQueryBuilder('u')
            ->where('u.name LIKE :q')
            ->andWhere('u.company = :company') // filtre ajouté
            ->setParameter('q', '%' . $q . '%')
            ->setParameter('company', $company)
            ->setMaxResults($limit)
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
