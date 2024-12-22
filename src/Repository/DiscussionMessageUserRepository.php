<?php

namespace App\Repository;

use App\Entity\DiscussionMessageUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<DiscussionMessageUser>
 *
 * @method DiscussionMessageUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiscussionMessageUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiscussionMessageUser[]    findAll()
 * @method DiscussionMessageUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscussionMessageUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiscussionMessageUser::class);
    }

//    /**
//     * @return DiscussionMessageUser[] Returns an array of DiscussionMessageUser objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DiscussionMessageUser
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
