<?php

namespace App\Controller\Discussion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\Discussion\DiscussionFormType;
use App\Services\Discussion\DiscussionService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Message;
use App\Entity\SearchDiscussion;
use App\Entity\Discussion;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;
use App\Controller\Pagination\Pagination;

class DiscussionController extends AbstractController
{
    #[Route('/user/discussion', name: 'app_discussion')]
    public function index(
        Request $request,
        RequestStack $requestStack, 
        DiscussionService $discussionService, 
    ) : Response
    {      
        $request = $requestStack->getMainRequest();

        $discussion = new Discussion();

        $discussionForm = $this->createForm(DiscussionFormType::class, $discussion);

        $discussionForm->handleRequest($request);

        if ($discussionForm->isSubmitted() && $discussionForm->isValid()) {
            return $discussionService->handleDiscussionFormData($discussionForm);
        }

        return $this->render('discussion/index.html.twig', [
            'formDiscussion' => $discussionForm->createView()
        ]);
    }

    #[Route('/user/list/discussion/{page}', name: 'app_list_discussion', options: ['expose' => true])]
    public function listDiscussions(
        Request $request,
        EntityManagerInterface $em,
        Environment $environment,
        DiscussionService $discussionService, 
        Security $security,
        Pagination $pagination,
        int $page = 1,
    ) : Response
    {        
        $user = $security->getUser();

        $page = $request->get('page'); 

        $criteria = $request->get('criteria');

        //$discussions = $em->getRepository(Discussion::class)->findDiscussion($user);

        $discussionPaginationInfos = $discussionService->searchDiscussions($page, $criteria);

        return new JsonResponse([
            'discussions' => $environment->render('discussion/list.html.twig', [
                'page' => $page,
                'numbrePagesPagination' => $discussionPaginationInfos['numbrePagesPagination'],
                'discussions' => $discussionPaginationInfos['data'],
            ]),
        ]);
    }

    #[Route('/user/search/discussion', name: 'app_search_discussion', options: ['expose' => true])]
    public function searchDiscussion(
        Request $request,
        EntityManagerInterface $em,
        Environment $environment,
        Security $security,
    ) : Response
    {        
        $user = $security->getUser();

        $searchDiscussion = $em->getRepository(SearchDiscussion::class)->findAll();

        return new JsonResponse([
            'html' => $environment->render('discussion/search_discussion_with_criteria.html.twig', [
                'searchDiscussion' => $searchDiscussion
            ]),
        ]);
    }
}
