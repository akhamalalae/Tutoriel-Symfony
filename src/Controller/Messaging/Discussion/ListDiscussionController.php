<?php

namespace App\Controller\Messaging\Discussion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Discussion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;
use App\Controller\Pagination\Pagination;
use App\Services\Breadcrumb\BreadcrumbService;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Services\Discussion\DiscussionSearchService;

#[IsGranted('ROLE_USER')]
class ListDiscussionController extends AbstractController
{
    public function __construct(
        private Environment $environment,
        private DiscussionSearchService $discussionSearchService
    ) {}

    #[Route('/user/list/discussion/{page}', name: 'app_list_discussion', options: ['expose' => true])]
    public function index(Request $request, int $page = 1) : Response
    {    
        try {
            $page = $request->get('page'); 

            $criteria = $request->get('criteria');

            $searchCriteria = $this->discussionSearchService->saveSearchDiscussion($criteria);

            $discussionPaginationInfos = $this->discussionSearchService->discussions($page, $searchCriteria['searchDiscussion'], $searchCriteria['saveSearch']);

            return new JsonResponse([
                'discussions' => $this->environment->render('discussion/list.html.twig', [
                    'page' => $page,
                    'numbrePagesPagination' => $discussionPaginationInfos['numbrePagesPagination'],
                    'discussions' => $discussionPaginationInfos['data'],
                ]),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue'], 500);
        }    
    }
}
