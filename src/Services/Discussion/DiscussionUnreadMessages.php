<?php

namespace App\Services\Discussion;

use App\Entity\Discussion;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\User\UserService;

use App\Contracts\Discussion\DiscussionUnreadMessagesInterface;

class DiscussionUnreadMessages implements DiscussionUnreadMessagesInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserService $userService,
    ) {}

     /**
     * get unread messages for each discussion for the current user.
     *
     * @param array $discussions
     * @param User $user
     * 
     * @return array DiscussionUnreadMessages
     */
    public function getDiscussionUnreadMessages(array $discussions, User $user): array
    {
        $totalUnreadMessages = 0;
        $discussionUnreadMessages = [];

        if (empty($discussions) || !$user) {
            return [
                'discussionUnreadMessages' => [],
                'numberTotalUnreadMessages' => 0,
            ];
        }

        foreach ($discussions as $discussion) {
            // Sécurité : on ignore si ce n’est pas une instance valide
            if (!$discussion instanceof Discussion) {
                continue;
            }

            // Récupération des messages non lus
            $unreadMessages = $this->em
                ->getRepository(Message::class)
                ->getUnreadMessages($discussion, $user);

            // Comptage global
            $totalUnreadMessages += \count($unreadMessages);

            // Ajout des données par discussion
            $discussionUnreadMessages[] = [
                'discussionId' => $discussion->getId(),
                'unreadMessages' => $unreadMessages,
            ];
        }

        // Retour propre et structuré
        return [
            'discussionUnreadMessages' => $discussionUnreadMessages,
            'numberTotalUnreadMessages' => $totalUnreadMessages,
        ];
    }
}