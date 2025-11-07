<?php
namespace App\Services\Discussion;

use Twig\Environment;
use App\Entity\Discussion;
use App\Entity\User;
use App\Entity\SearchDiscussion;
use Symfony\Component\Form\FormInterface;
use App\Services\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Contracts\Discussion\DiscussionRendererInterface;
use App\Contracts\Discussion\DiscussionSearchInterface;
use App\Contracts\Discussion\DiscussionUnreadMessagesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DiscussionRenderer extends AbstractController implements DiscussionRendererInterface
{
    public function __construct(
        private readonly Environment $environment,
        private readonly EntityManagerInterface $em,
        private readonly UserService $userService,
        private readonly DiscussionSearchInterface $searchService,
        private readonly DiscussionUnreadMessagesInterface $DiscussionUnreadMessages,
    ) {}

    public function renderDiscussionForm(FormInterface $form): Response
    {
        return $this->render('discussion/index.html.twig', [
            'formDiscussion' => $form->createView()
        ]);
    }

    public function renderDiscussionMessageNavBar(array $discussions): Response
    {
        $user = $this->userService->getAuthenticatedUser();
        
        $unreadMessages = $this->unreadMessages($discussions, $user);

        return $this->render('nav-bar/messages.html.twig', [
            'messages' => $unreadMessages['discussionUnreadMessages'],
            'numbreMessages' => $unreadMessages['numberTotalUnreadMessages']
        ]);
    }

    public function renderListDiscussion(int $page, ?SearchDiscussion $searchDiscussion): string
    {
        $user = $this->userService->getAuthenticatedUser();
        
        $discussions = $this->searchService
            ->discussions($page, $searchDiscussion);
        
        $unreadMessages = $this->unreadMessages($discussions['data'], $user);

        return $this->environment->render('discussion/list.html.twig', [
            'page' => $page,
            'totalPages' => $discussions['totalPages'],
            'discussions' => $discussions['data'],
            'discussionUnreadMessages' => $unreadMessages['discussionUnreadMessages'],
            'numberTotalUnreadMessages' => $unreadMessages['numberTotalUnreadMessages']
        ]);
    }

    private function unreadMessages(array $discussions, User $user): array
    {
        return $this->DiscussionUnreadMessages
            ->getDiscussionUnreadMessages($discussions, $user);
    }
}
