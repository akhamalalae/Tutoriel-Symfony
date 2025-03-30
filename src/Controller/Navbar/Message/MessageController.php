<?php

namespace App\Controller\Navbar\Message;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Services\Discussion\DiscussionSearchService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    public function __construct(
        private readonly DiscussionSearchService $discussionSearchService
    ) {}

    #[Route('/navbar/messages', name: 'app_navbar_messages')]
    public function messagesAction(): Response
    {
        $unreadMessages = $this->discussionSearchService->searchMessagesNavBar();

        return $this->render('nav-bar/messages.html.twig', [
            'messages' => $unreadMessages,
            'numbreMessages' => count($unreadMessages)
        ]);
    }
}
