<?php

namespace App\Controller\Messaging\Discussion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\Discussion\DiscussionFormType;
use App\Services\Discussion\DiscussionService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\SearchDiscussion;
use App\Entity\Discussion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;
use App\Services\Breadcrumb\BreadcrumbService;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Services\Discussion\DiscussionSearchService;
use App\Services\User\UserService;

#[IsGranted('ROLE_USER')]
class SearchDiscussionController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private EntityManagerInterface $em,
        private Environment $environment
    ) {}

    #[Route('/user/search/discussion', name: 'app_search_discussion', options: ['expose' => true])]
    public function index(Request $request) : Response
    {        
        $user = $this->userService->getAuthenticatedUser();

        $idSearchDiscussion = $request->get('idSearchDiscussion');
        
        $selectedSearchDiscussion = $idSearchDiscussion ? $this->em->getRepository(SearchDiscussion::class)->find($idSearchDiscussion) : null;

        $searchDiscussion = $this->em->getRepository(SearchDiscussion::class)->findBy(['creatorUser' => $user]);

        return new JsonResponse([
            'html' => $this->environment->render('discussion/discussion_search.html.twig', [
                'searchDiscussion' => $searchDiscussion,
                'selectedSearchDiscussion' => $selectedSearchDiscussion
            ]),
        ]);
    }
}
