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

    public function findUsersWithBirthdayToday(): array
    {
        $today = (new \DateTime())->format('m-d');

        $qb = $this->createQueryBuilder('u');
        $qb->where('DATE_FORMAT(u.dateOfBirth, \'%m-%d\') = :today')
            ->setParameter('today', $today);

        return $qb->getQuery()->getResult();
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
     * Query to find users for the discussion form, excluding the current user
     * and users who already have a discussion with the current user.
     */
    public function findUsersDiscussionForm(User $currentUser): QueryBuilder
    {
        //$subQb1 retourne les destinataires avec lesquels l’utilisateur courant a déjà une discussion.
        $subQb1 = $this->_em->createQueryBuilder()
            ->select('IDENTITY(d1.personInvitationRecipient)')
            ->from('App\Entity\Discussion', 'd1')
            ->where('d1.personInvitationSender = :currentUser');

        $subQb2 = $this->_em->createQueryBuilder()
            ->select('IDENTITY(d2.personInvitationSender)')
            ->from('App\Entity\Discussion', 'd2')
            ->where('d2.personInvitationRecipient = :currentUser');

        // le QueryBuilder principal
        $qb = $this->createQueryBuilder('u');

        $qb->where('u != :currentUser')
            // Ne sélectionne aucun utilisateur (u) dont l’ID se trouve dans le résultat de la sous-requête $subQb1.
            ->andWhere($qb->expr()->notIn('u.id', $subQb1->getDQL()))
            ->andWhere($qb->expr()->notIn('u.id', $subQb2->getDQL()))
            ->setParameter('currentUser', $currentUser)
            ->orderBy('u.firstName', 'ASC');

        // Résultat : retourne tous les utilisateurs sauf : 
        //le user connecté (u != :user)les users avec qui une discussion existe déjà.

        return $qb;
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
