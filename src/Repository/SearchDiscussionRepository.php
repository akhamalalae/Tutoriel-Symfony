<?php

namespace App\Repository;

use App\Entity\SearchDiscussion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SearchDiscussion>
 *
 * @method SearchDiscussion|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchDiscussion|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchDiscussion[]    findAll()
 * @method SearchDiscussion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchDiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchDiscussion::class);
    }

//    /**
//     * @return SearchDiscussion[] Returns an array of SearchDiscussion objects
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

//    public function findOneBySomeField($value): ?SearchDiscussion
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
