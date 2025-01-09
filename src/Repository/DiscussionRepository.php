<?php

namespace App\Repository;

use App\Entity\Discussion;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Discussion>
 *
 * @method Discussion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discussion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discussion[]    findAll()
 * @method Discussion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discussion::class);
    }

    /**
    * @return Discussion[] Returns an array of Discussion objects
    */
    public function findDiscussion(User $user): array
    {
        $discussions = $this->createQueryBuilder('d');

        $activeDiscussions = $this->messagesNavBar($user);

        if ($activeDiscussions) {
            $array = array();
    
            foreach ($activeDiscussions as $item) {
                array_push($array, $item->getId());
            }

            $discussions->andWhere('d.id NOT IN (:array)')
            ->setParameter('array', $array);
        }
        
        $discussions->andWhere('d.personOne = :user')
            ->orWhere('d.personTwo = :user')
            ->setParameter('user', $user)
            ->orderBy('d.dateCreation', 'DESC')
        ;

        return [
            "discussions" => array_merge($activeDiscussions, $discussions->getQuery()->getResult()),
            "countActiveDiscussions" => count($activeDiscussions)
        ];
    }

    public function messagesNavBar(User $user) : array
    {
        return $this->createQueryBuilder('discussion')
            ->andWhere('
                :user = discussion.personOne
                AND
                discussion.personTwoNumberUnreadMessages IS NOT NULL
            ')
            ->orWhere('
                :user = discussion.personTwo
                AND
                discussion.personOneNumberUnreadMessages IS NOT NULL
            ')
            ->setParameter('user', $user->getId())
            ->orderBy('discussion.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Discussion[] Returns an array of Discussion objects
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

//    public function findOneBySomeField($value): ?Discussion
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
