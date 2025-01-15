<?php

namespace App\Controller\Navbar\Message;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Services\Discussion\DiscussionService;
use App\Entity\Discussion;

class MessageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em, 
        private DiscussionService $discussionService,
        private Security $security) 
    {}

    public function messagesAction()
    {
        $user = $this->security->getUser();

        $unreadMessages = $this->discussionService->searchMessagesNavBar();

        return $this->render('nav-bar/messages.html.twig', [
            'messages' => $unreadMessages,
            'numbreMessages' => count($unreadMessages)
        ]);
    }
}
