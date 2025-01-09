<?php

namespace App\Repository;

use App\Entity\SearchMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SearchMessage>
 *
 * @method SearchMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchMessage[]    findAll()
 * @method SearchMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchMessage::class);
    }

//    /**
//     * @return SearchMessage[] Returns an array of SearchMessage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SearchMessage
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
