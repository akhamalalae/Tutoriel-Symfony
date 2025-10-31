<?php

namespace App\Controller\Messaging\Discussion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Discussion;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;
use App\Controller\Pagination\Pagination;
use App\Services\Breadcrumb\BreadcrumbService;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Services\Discussion\DiscussionSearchService;
use App\Services\Discussion\DiscussionService;
use App\Services\User\UserService;

#[IsGranted('ROLE_USER')]
class ListDiscussionController extends AbstractController
{
    public function __construct(
        private readonly Environment $environment,
        private readonly UserService $userService,
        private readonly EntityManagerInterface $em,
        private readonly DiscussionSearchService $discussionSearchService,
        private readonly DiscussionService $discussionService
    ) {}

    #[Route('/user/list/discussion/{page}', name: 'app_list_discussion', options: ['expose' => true])]
    public function index(Request $request, int $page = 1) : Response
    {    
        try {
            $user = $this->userService->getAuthenticatedUser();

            $page = $request->get('page'); 

            $criteria = $request->get('criteria');

            $searchDiscussion = $this->discussionSearchService
                ->saveSearch($criteria);

            $discussions = $this->discussionSearchService
                ->discussions($page, $searchDiscussion);
            
            $unreadMessages = $this->discussionService
                ->getDiscussionUnreadMessages($discussions['data'], $user);

            return new JsonResponse([
                'discussions' => $this->environment->render('discussion/list.html.twig', [
                    'page' => $page,
                    'totalPages' => $discussions['totalPages'],
                    'discussions' => $discussions['data'],
                    'discussionUnreadMessages' => $unreadMessages['discussionUnreadMessages'],
                    'numberTotalUnreadMessages' => $unreadMessages['numberTotalUnreadMessages']
                ]),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue'], 500);
        }    
    }
}
