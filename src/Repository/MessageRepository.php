<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Discussion;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * get unread messages of a discussion for the current user.
     *
     * @param Discussion $discussion
     * @param User $currentUser
     * @return Message[] Messages
     */
    public function getUnreadMessages(Discussion $discussion, User $currentUser): array
    {
        $isSender = $discussion->getPersonInvitationSender() === $currentUser;
        $targetUser = $isSender
            ? $discussion->getPersonInvitationRecipient()
            : $discussion->getPersonInvitationSender();

        if (!$targetUser) {
            return [];
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('m')
            ->from(Message::class, 'm')
            ->join('App\Entity\DiscussionMessageUser', 'dmu', 'WITH', 'dmu.message = m')
            ->where('dmu.discussion = :discussion')
            ->andWhere('m.isRead = false')
            ->andWhere('m.creatorUser = :targetUser')
            ->setParameters([
                'discussion' => $discussion,
                'targetUser' => $targetUser,
            ]);

        $unreadMessages = $qb->getQuery()->getResult();

        if (empty($unreadMessages)) {
            return [];
        }

        return $unreadMessages;
    }

//    /**
//     * @return Message[] Returns an array of Message objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Message
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
