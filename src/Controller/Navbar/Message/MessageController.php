<?php

namespace App\Controller\Navbar\Message;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Discussion;
use App\Services\User\UserService;
use App\Entity\Message;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Services\Discussion\DiscussionService;

#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private UserService $userService,
        private readonly DiscussionService $discussionService
    ) {}

    #[Route('/navbar/messages', name: 'app_navbar_messages')]
    public function messagesAction(): Response
    {
        $user = $this->userService->getAuthenticatedUser();
        
        $discussions = $this->em
            ->getRepository(Discussion::class)
            ->findDiscussion($user);
        
        $unreadMessages = $this->discussionService
            ->getDiscussionUnreadMessages($discussions, $user);

        return $this->render('nav-bar/messages.html.twig', [
            'messages' => $unreadMessages['discussionUnreadMessages'],
            'numbreMessages' => $unreadMessages['numberTotalUnreadMessages']
        ]);
    }
}
