<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\ORM\QueryBuilder;


/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
    * @return QueryBuilder Returns an QueryBuilder of User objects
    */
    public function findUsersDiscussionForm(User $user, array $discussions): QueryBuilder
    {
        $findExistUsers = $this->findExistUsersDiscussion($discussions);

        $createQueryBuilder = $this->createQueryBuilder('u')
            ->leftJoin('u.discussionsPersonOne', 'dPersonOne')
            ->leftJoin('u.discussionsPersonTwo', 'dPersonTwo')
            ->leftJoin('dPersonTwo.personTwo', 'pTwo')
            ->leftJoin('dPersonOne.personOne', 'pOne')
        ;

        if ($findExistUsers) {
            $createQueryBuilder->andwhere('u NOT IN (:existUsers)')
                ->setParameter('existUsers', $findExistUsers)
            ;
        }

        return $createQueryBuilder;
    }

    /**
    * @return array Returns an array of Id User
    */
    public function findExistUsersDiscussion(array $discussions): array
    {
        $existUsersDiscussion = array();

        foreach ($discussions as $item) {
            $personOne = $item->getPersonOne()->getId();
            $personTwo = $item->getPersonTwo()->getId();

            if (! in_array($personOne, $existUsersDiscussion)) {
                array_push($existUsersDiscussion, $personOne);
            }

            if (! in_array($personTwo, $existUsersDiscussion)) {
                array_push($existUsersDiscussion, $personTwo);
            }
        }

        return $existUsersDiscussion;
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
