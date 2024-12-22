<?php

namespace App\Controller\Navbar\Messaging;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\Discussion;

class MessageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em, 
        private Security $security) 
    {}

    public function messagesAction()
    {
        $user = $this->security->getUser();

        $messagesNavBar = $this->em->getRepository(Discussion::class)->messagesNavBar($user);

        $arrayUnreadMessages = [];

        foreach ($messagesNavBar as $discussion) {
            $discussionMessageUsers = array_reverse($discussion->getDiscussionMessageUsers()->getValues());

            $numberUnreadMessages = $discussion->getPersonOneNumberUnreadMessages() + $discussion->getPersonTwoNumberUnreadMessages();

            for ($i = 0; $i < $numberUnreadMessages; $i++) {
                array_push(
                    $arrayUnreadMessages, 
                    $discussionMessageUsers[$i]
                );
            }
        }

        return $this->render('nav-bar/messages.html.twig', [
            'messages' => $arrayUnreadMessages,
            'numbreMessages' => count($arrayUnreadMessages)
        ]);
    }
}
